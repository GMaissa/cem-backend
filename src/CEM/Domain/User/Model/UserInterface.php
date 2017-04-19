<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Domain.User
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
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
