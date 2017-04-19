<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.UI.ApiBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Ui\ApiBundle\Model;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Api Error model
 */
class ApiError
{
    /**
     * @var integer
     *
     * @Serializer\Since("1.0")
     * @Serializer\Groups({"details", "list"})
     * @Assert\Choice({"400", "404", "500"})
     * @Assert\NotBlank()
     */
    private $code;

    /**
     * @var string
     *
     * @Serializer\Since("1.0")
     * @Serializer\Groups({"details", "list"})
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * Set error code
     *
     * @param integer $code
     *
     * @return ApiError
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get error code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set error message
     *
     * @param string $message
     *
     * @return ApiError
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
