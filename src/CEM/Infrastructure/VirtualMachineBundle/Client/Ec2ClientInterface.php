<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package  CEM.Domain.VirtualMachine
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace CEM\Infrastructure\VirtualMachineBundle\Client;

/**
 * AWS Ec2 client wrapper class
 */
interface Ec2ClientInterface
{
    /**
     * Retrieve Ec2 Instances
     *
     * @param array $filters
     *
     * @return Object
     */
    public function describeInstances($filters = array());

    /**
     * Start Ec2 Instances
     *
     * @param array $filters
     *
     * @return Object
     */
    public function startInstances($filters = array());

    /**
     * Stop Ec2 Instances
     *
     * @param array $filters
     *
     * @return Object
     */
    public function stopInstances($filters = array());
}
