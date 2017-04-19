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

namespace CEM\Infrastructure\VirtualMachineBundle\Client\Wrapper;

use Aws\Ec2\Ec2Client;
use Aws\Result;
use CEM\Infrastructure\VirtualMachineBundle\Client\Ec2ClientInterface;

/**
 * AWS Ec2 client wrapper class
 */
class Ec2ClientWrapper implements Ec2ClientInterface
{
    /**
     * Ec2 API client
     * @var Ec2Client
     */
    private $client;

    /**
     * Register the EC2 API client object
     *
     * @param Ec2Client $client
     */
    public function setClient(Ec2Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve Ec2 Instances
     *
     * @param array $filters
     *
     * @return Result
     */
    public function describeInstances($filters = array()): Result
    {
        return $this->client->describeInstances($filters);
    }

    /**
     * Start Ec2 Instances
     *
     * @param array $filters
     *
     * @return Result
     */
    public function startInstances($filters = array()): Result
    {
        return $this->client->startInstances($filters);
    }

    /**
     * Stop Ec2 Instances
     *
     * @param array $filters
     *
     * @return Result
     */
    public function stopInstances($filters = array()): Result
    {
        return $this->client->stopInstances($filters);
    }
}
