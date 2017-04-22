<?php
/**
 * File part of the Cloud Environments Management Backend
 *
 * @category  CEM
 * @package   CEM.Infrastructure.VirtualMachineBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Infrastructure\VirtualMachineBundle\Repository;

use CEM\Domain\VirtualMachine\Exception\VmNotFoundException;
use CEM\Domain\VirtualMachine\Factory\VmFactoryInterface;
use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;
use CEM\Domain\VirtualMachine\Repository\VmRepositoryInterface;
use CEM\Infrastructure\VirtualMachineBundle\Client\Ec2ClientInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use CEM\Infrastructure\VirtualMachineBundle\Event\VmStateUpdateEvent;

/**
 * Ec2 virtual machine repository class
 */
class Ec2VmRepository implements VmRepositoryInterface
{
    /**
     * Ec2 API client
     * @var Ec2ClientInterface
     */
    private $apiClient;

    /**
     * @var VmFactoryInterface
     */
    private $vmFactory;

    /**
     * Mapping with Ec2 states
     * @var array
     */
    private $statesMapping = [];

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Ec2ProviderAdapter constructor.
     */
    public function __construct()
    {
        $this->statesMapping = [
            VirtualMachineInterface::STATE_STARTED  => '16',
            VirtualMachineInterface::STATE_STOPPED  => '80',
            VirtualMachineInterface::STATE_STARTING => '0',
            VirtualMachineInterface::STATE_STOPPING => '64',
        ];
    }

    /**
     * Register the EC2 API client wrapper object
     *
     * @param Ec2ClientInterface $apiClient
     */
    public function setApiClient(Ec2ClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Register the virtual machine factory
     *
     * @param VmFactoryInterface $vmFactory
     */
    public function setVmFactory(VmFactoryInterface $vmFactory)
    {
        $this->vmFactory = $vmFactory;
        $this->vmFactory->setStatesMapping($this->statesMapping);
    }

    /**
     * Register the event dispatcher object
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->findBy();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy($filters = array())
    {
        $virtualMachines    = [];
        $formattedFilters   = $this->formatFilters($filters);

        $virtualMachinesTmp = $this->getInstances($formattedFilters);

        foreach ($virtualMachinesTmp as $virtualMachineTmp) {
            $virtualMachines[] = $this->vmFactory->build($virtualMachineTmp);
        }

        return $virtualMachines;
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $vmId): VirtualMachineInterface
    {
        $response = $this->apiClient->describeInstances(['InstanceIds' => [$vmId]]);

        if (!isset($response['Reservations'][0]['Instances'][0])) {
            throw new VmNotFoundException(
                sprintf('No virtual machine found with Id: %s', $vmId)
            );
        }

        return $this->vmFactory->build($response['Reservations'][0]['Instances'][0]);
    }

    /**
     * {@inheritdoc}
     */
    public function save(VirtualMachineInterface $virtualMachine): VirtualMachineInterface
    {
        $action = 'start';
        if ($virtualMachine->isStopped()) {
            $action = 'stop';
        }

        $this->apiClient->{$action . 'Instances'}(
            ['InstanceIds' => [$virtualMachine->getId()]]
        );

        do {
            sleep(1);
            $response = $this->apiClient->describeInstances(['InstanceIds' => [$virtualMachine->getId()]]);
            $vmData   = $response['Reservations'][0]['Instances'][0];
        } while ((int)$vmData['State']['Code'] !== (int)$this->statesMapping[VirtualMachineInterface::STATE_STARTED] &&
                 (int)$vmData['State']['Code'] !== (int)$this->statesMapping[VirtualMachineInterface::STATE_STOPPED]);

        $publicIp = '';
        if (isset($vmData['PublicIpAddress'])) {
            $publicIp = $vmData['PublicIpAddress'];
        }

        $virtualMachine->setPublicIp($publicIp);

        $event = new VmStateUpdateEvent($virtualMachine);
        $this->eventDispatcher->dispatch(VmStateUpdateEvent::NAME, $event);

        return $virtualMachine;
    }

    /**
     * Format filters for Ec2 client
     *
     * @param array $filters
     *
     * @return array
     */
    protected function formatFilters($filters = array()): array
    {
        $formattedFilters = [];
        if (isset($filters['vmTypes'])) {
            $formattedFilters['Filters'][] = ['Name' => 'tag:environment', 'Values' => $filters['vmTypes']];
        }
        if (isset($filters['vmIds'])) {
            $formattedFilters['InstanceIds'] = $filters['vmIds'];
        }
        if (isset($filters['keepAlive'])) {
            $formattedFilters['Filters'][] = ['Name' => 'tag:keep-alive', 'Values' => $filters['keepAlive']];
        }
        if (isset($filters['autoStart'])) {
            $formattedFilters['Filters'][] = ['Name' => 'tag:auto-start', 'Values' => $filters['autoStart']];
        }
        if (isset($filters['states'])) {
            $formattedStates               = $this->convertStatesToEc2($filters['states']);
            $formattedFilters['Filters'][] = ['Name' => 'instance-state-code', 'Values' => $formattedStates];
        }

        return $formattedFilters;
    }

    /**
     * Format Virtual Machine state for Ec2 client
     *
     * @param array $types
     *
     * @return array
     */
    protected function convertStatesToEc2($types = array()): array
    {
        $formattedStates = [];
        foreach ($types as $type) {
            if (isset($this->statesMapping[$type])) {
                $formattedStates[] = $this->statesMapping[$type];
            }
        }

        return $formattedStates;
    }

    /**
     * Retrieve Ec2 instances
     *
     * @param array       $formattedFilters
     * @param array       $virtualMachinesTmp
     * @param string|null $nextToken
     *
     * @return array
     */
    private function getInstances(
        $formattedFilters = array(),
        $virtualMachinesTmp = array(),
        $nextToken = null
    ): array {
        $filters = $formattedFilters;
        if (!is_null($nextToken)) {
            $filters = array_merge($formattedFilters, ['NextToken' => $nextToken]);
        }

        $response = $this->apiClient->describeInstances($filters);
        foreach ($response['Reservations'] as $reservation) {
            $virtualMachinesTmp = array_merge($virtualMachinesTmp, $reservation['Instances']);
        }

        if (isset($response['NextToken']) && $response['NextToken'] != '') {
            $virtualMachinesTmp = $this->getInstances($formattedFilters, $virtualMachinesTmp, $response['NextToken']);
        };

        return $virtualMachinesTmp;
    }
}
