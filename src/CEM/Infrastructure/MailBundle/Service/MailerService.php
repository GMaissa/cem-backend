<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Infrastructure.MailBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
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
