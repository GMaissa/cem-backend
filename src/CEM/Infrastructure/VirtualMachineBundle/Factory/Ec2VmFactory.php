<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Infrastructure.VirtualMachineBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Infrastructure\VirtualMachineBundle\Factory;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use CEM\Domain\VirtualMachine\Factory\VmFactoryInterface;
use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;
use CEM\Infrastructure\VirtualMachineBundle\Event\VmPostLoadEvent;

/**
 * Ec2 virtual machine factory class
 */
class Ec2VmFactory implements VmFactoryInterface
{
    /**
     * @var string
     */
    private $vmClass;

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
     * Set the virtual machine class name
     *
     * @param string $class
     */
    public function setVmClass($class)
    {
        $this->vmClass = $class;
    }

    /**
     * Retrieve Ec2 states mapping
     *
     * @param array $statesMapping
     */
    public function setStatesMapping(array $statesMapping)
    {
        $this->statesMapping = $statesMapping;
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
    public function build(array $data = null): VirtualMachineInterface
    {
        $vmData = [
            'id'        => $data['InstanceId'],
            'type'      => $data['InstanceType'],
            'state'     => (int)array_search($data['State']['Code'], $this->statesMapping),
            'az'        => $data['Placement']['AvailabilityZone'],
            'privateIp' => $data['PrivateIpAddress'],
        ];

        if (isset($data['PublicIpAddress'])) {
            $vmData['publicIp'] = $data['PublicIpAddress'];
        }

        $vmData = $this->getVmDataFromTags($data, $vmData);

        $virtualMachine = new $this->vmClass($vmData);

        $event = new VmPostLoadEvent($virtualMachine);
        $this->eventDispatcher->dispatch(VmPostLoadEvent::NAME, $event);

        return $virtualMachine;
    }

    /**
     * Retrieve virtual machine properties from instance tags
     *
     * @param array $data
     * @param array $vmData
     *
     * @return mixed
     */
    private function getVmDataFromTags(array $data, array $vmData): array
    {
        if (isset($data['Tags'])) {
            foreach ($data['Tags'] as $tag) {
                if ($tag['Key'] == 'Name') {
                    $vmData['name'] = $tag['Value'];
                }
                if ($tag['Key'] == 'mailinglist') {
                    $vmData['mailingList'] = explode(',', $tag['Value']);
                }
                if ($tag['Key'] == 'keep-alive') {
                    $vmData['keepAlive'] = (bool)$tag['Value'];
                }
                if ($tag['Key'] == 'auto-start') {
                    $vmData['autoStart'] = (bool)$tag['Value'];
                }
            }
        }

        return $vmData;
    }
}
