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

namespace CEM\Infrastructure\MailBundle\Service;

/**
 * Service to send mail
 */
class MailerService
{
    /**
     * Mailer object
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * Sets the mailer.
     *
     * @param \Swift_Mailer $mailer
     */
    public function setMailer(\Swift_Mailer $mailer = null)
    {
        $this->mailer = $mailer;
    }

    public function createMessage()
    {
        return $this->mailer->createMessage();
    }

    public function send($message)
    {
        $this->mailer->send($message);
    }
}
