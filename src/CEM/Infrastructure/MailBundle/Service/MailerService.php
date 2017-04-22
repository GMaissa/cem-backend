<?php
/**
 * File part of the Cloud Environments Management Backend
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

    /**
     * Create the message object to be sent
     *
     * @return object
     */
    public function createMessage()
    {
        return $this->mailer->createMessage();
    }

    /**
     * Send the provided message
     *
     * @param object $message
     */
    public function send($message)
    {
        $this->mailer->send($message);
    }
}
