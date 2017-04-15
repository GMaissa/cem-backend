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

namespace CEM\Domain\User\Model;

/**
 * User Model interface
 */
interface UserInterface
{
    /**
     * User constructor
     *
     * @param array $properties
     */
    public function __construct($properties = array());

    /**
     * Get User full name
     *
     * @return string
     */
    public function getFullName(): string;
}
