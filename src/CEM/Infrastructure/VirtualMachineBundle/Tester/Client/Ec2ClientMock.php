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

namespace CEM\Infrastructure\VirtualMachineBundle\Tester\Client;

use CEM\Infrastructure\VirtualMachineBundle\Client\Ec2ClientInterface;

/**
 * AWS Ec2 client mock class
 *
 * @codeCoverageIgnore
 */
class Ec2ClientMock implements Ec2ClientInterface
{
    private $instances = [
        'i-00000001' => [
            'publicIp' => '192.168.1.1'
        ],
        'i-00000002' => [
            'publicIp' => '192.168.1.2'
        ]
    ];

    private $responseSkeleton = [
        "Reservations" => [
            [
                "ReservationId" => "r-00000001",
                "OwnerId" => "123456789012",
                "Instances" => []
            ]
        ]
    ];

    /**
     * Class constructor
     */
    public function __construct()
    {
        defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
        foreach (array_keys($this->instances) as $instanceId) {
            $this->instances[$instanceId]['data'] = \GuzzleHttp\json_decode(
                file_get_contents(
                    __DIR__ . DS . "fixtures" . DS . "instances" . DS . $instanceId . '.json'
                ),
                true
            );
        }
    }

    /**
     * Unset the api client object as we don't need it
     *
     * @param mixed $client
     */
    public function setClient($client)
    {
        // Nothing to do
        unset($client);
    }

    /**
     * {@inheritdoc}
     */
    public function describeInstances($filters = array()): array
    {
        $response = $this->responseSkeleton;
        if ($filters == []) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000001']['data'];
            $response['NextToken'] = '1234';
        }
        if ($filters == ['NextToken' => '1234']) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000002']['data'];
        }
        if ($filters == ['Filters' => [['Name' => 'tag:keep-alive', 'Values' => true]]]) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000001']['data'];
        }
        if ($filters == ['Filters' => [['Name' => 'tag:auto-start', 'Values' => true]]]) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000002']['data'];
        }
        if ($filters == ['Filters' => [['Name' => 'instance-state-code', 'Values' => ['16']]]]) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000002']['data'];
        }
        if ($filters == ['Filters' => [['Name' => 'instance-state-code', 'Values' => ['80']]]]) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000001']['data'];
        }
        if ($filters == ['Filters' => [['Name' => 'tag:environment', 'Values' => ['development']]]]) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000001']['data'];
        }
        if ($filters == ['InstanceIds' => ['i-00000001']]) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000001']['data'];
        }
        if ($filters == ['InstanceIds' => ['i-00000002']]) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000002']['data'];
        }
        if ($filters == ['InstanceIds' => ['i-00000001', 'i-00000002']]) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000001']['data'];
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000002']['data'];
        }
        $autoStartFilters = [
            'Filters' => [
                ['Name' => 'tag:environment', 'Values' => ['development']],
                ['Name' => 'tag:auto-start', 'Values' => [true]],
                ['Name' => 'instance-state-code', 'Values' => ['80']]
            ]
        ];
        if ($filters == $autoStartFilters) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000001']['data'];
        }
        $autoStopFilters = [
            'Filters' => [
                ['Name' => 'tag:environment', 'Values' => ['development']],
                ['Name' => 'tag:keep-alive', 'Values' => [false]],
                ['Name' => 'instance-state-code', 'Values' => ['16']]
            ]
        ];
        if ($filters == $autoStopFilters) {
            $response['Reservations'][0]['Instances'][] = $this->instances['i-00000002']['data'];
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function startInstances($filters = array())
    {
        if (isset($filters['InstanceIds'])) {
            foreach ($filters['InstanceIds'] as $instanceId) {
                if ($this->instances[$instanceId]['data']['State']['Code'] == '80') {
                    $this->instances[$instanceId]['data']['State']['Code'] = '16';
                    $this->instances[$instanceId]['data']['State']['Name'] = 'running';
                    $this->instances[$instanceId]['data']['PublicIpAddress'] =
                        $this->instances[$instanceId]['publicIp'];
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stopInstances($filters = array())
    {
        if (isset($filters['InstanceIds'])) {
            foreach ($filters['InstanceIds'] as $instanceId) {
                if ($this->instances[$instanceId]['data']['State']['Code'] == '16') {
                    $this->instances[$instanceId]['data']['State']['Code'] = '80';
                    $this->instances[$instanceId]['data']['State']['Name'] = 'stopped';
                    unset($this->instances[$instanceId]['data']['PublicIpAddress']);
                }
            }
        }
    }
}
