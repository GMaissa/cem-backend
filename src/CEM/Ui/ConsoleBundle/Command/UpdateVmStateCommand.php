<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Domain.VirtualMachine
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace CEM\Ui\ConsoleBundle\Command;

use CEM\Domain\VirtualMachine\Exception\VmNotFoundException;
use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;
use CEM\Domain\VirtualMachine\Repository\VmRepositoryInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to update all EC2 instances depending on their tags
 */
class UpdateVmStateCommand extends AbstractCommand
{
    /**
     * @var array
     */
    private $vmFiltersPerAction = [];

    /**
     * @var bool
     */
    private $notify = false;

    /**
     * @var string
     */
    private $action;

    /**
     * @var VmRepositoryInterface
     */
    private $vmRepository;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->vmFiltersPerAction = [
            'start' => [
                'autoStart' => [1],
                'states' => [VirtualMachineInterface::STATE_STOPPED],
                'vmTypes' => ['development']
            ],
            'stop' => [
                'keepAlive' => ['0'],
                'states' => [VirtualMachineInterface::STATE_STARTED],
                'vmTypes' => ['development']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('vms:dev:update')
            ->setDescription('Update all development virtual machines depending on their tags')
            ->addOption(
                'notify',
                null,
                InputOption::VALUE_NONE,
                'Notify projects mailing lists of virtual machines status update'
            )
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                sprintf(
                    'Action to be executed on virtual machines (between : %s)',
                    implode(', ', array_keys($this->vmFiltersPerAction))
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $inputOptions = $input->getOptions();
        if (isset($inputOptions['notify']) && $inputOptions['notify']) {
            $this->notify = true;
        }

        $action = $input->getArgument('action');
        if (!array_key_exists($action, $this->vmFiltersPerAction)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid action %s. Select action between the following: \n - %s",
                    $action,
                    implode("\n - ", array_keys($this->vmFiltersPerAction))
                )
            );
        }
        $this->action = $action;
        $this->vmRepository = $this->getContainer()->get("vm_dashboard.vm.repository");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vms = $this->vmRepository->findBy(
            $this->vmFiltersPerAction[$this->action]
        );

        if (!count($vms)) {
            return;
        }

        $vmNames = [];
        foreach ($vms as $virtualMachine) {
            $vmNames[] = $virtualMachine->getName();
        }

        $this->outputMsgInfo(
            sprintf(
                "%s following instances:\n - %s\n",
                $this->action,
                implode("\n - ", $vmNames)
            )
        );

        foreach ($vms as $virtualMachine) {
            $this->updateVmState($virtualMachine);
        }
    }

    /**
     * Update instance state
     *
     * @param VirtualMachineInterface $virtualMachine
     */
    protected function updateVmState($virtualMachine)
    {
        try {
            $virtualMachine->{$this->action}();
            $this->vmRepository->save($virtualMachine);
        } catch (VmNotFoundException $e) {
            $this->outputMsgError($e->getMessage());
        } catch (\Swift_RfcComplianceException $e) {
            $this->outputMsgError(
                sprintf("Invalid mailing list for instance : %s", $virtualMachine->getName())
            );
        }
    }
}
