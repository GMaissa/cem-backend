<?php
/**
 * File part of the Cloud Environments Management Backend
 *
 * @category  CEM
 * @package   CEM.Behat
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Behat\Context;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Exception;
use Swift_Message;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * OAuth Context
 */
class MailContext implements KernelAwareContext
{
    private $kernel;
    private $message;

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernelInterface)
    {
        $this->kernel = $kernelInterface;
    }

    /**
     * @param string       $messageId
     * @param string       $mailTo
     * @param PyStringNode $body
     *
     * @throws Exception
     *
     * @Then the :messageId mail sent to :mailTo should contain:
     */
    public function theMailSentToShouldContain($messageId, $mailTo, PyStringNode $body)
    {
        $recipients = [];
        foreach (explode(',', $mailTo) as $recipient) {
            $recipients[] = [$recipient];
        }
        $this->mailShouldHaveBeenSent($messageId);
        $this->theMailRecipentsShouldInclude(new TableNode($recipients));
        $this->theMailBodyShouldBe($body);
    }

    /**
     * @Then the :messageId mail should have been sent
     */
    public function mailShouldHaveBeenSent($messageId)
    {
        $this->message = $this->findMessage($messageId);

        if (!$this->message) {
            throw new Exception(sprintf('The message "%s" has not been sent.', $messageId));
        }
    }

    /**
     * @Then the :messageId mail should not have been sent
     */
    public function mailShouldNotHaveBeenSent($messageId)
    {
        $message = $this->findMessage($messageId);

        if ($message) {
            throw new Exception(sprintf('The message "%s" has been sent.', $messageId));
        }
    }

    /**
     * @Then the mail recipients should include:
     */
    public function theMailRecipientsShouldInclude(TableNode $expectedRecipients)
    {
        $expectedRecipients = array_keys($expectedRecipients->getRowsHash());
        $recipients = array_keys($this->message->getTo());
        if ($expectedRecipients != array_intersect($recipients, $expectedRecipients)) {
            throw new Exception(
                sprintf(
                    'The message was not sent to recipients %s',
                    implode(",", $expectedRecipients)
                )
            );
        }
    }

    /**
     * @Then the mail body should be:
     */
    public function theMailBodyShouldBe(PyStringNode $body)
    {
        if ($this->message->getBody() === null ||
            trim(strip_tags($this->message->getBody())) != $this->replaceParams($body->getRaw())
        ) {
            throw new Exception(
                sprintf(
                    "The body for message is not the expected one. Received body is :\n%s",
                    $this->message->getBody()
                )
            );
        }
    }

    /**
     * Retrieve message from Spool dir
     *
     * @param string $messageId
     *
     * @return bool|Swift_Message
     */
    private function findMessage($messageId)
    {
        $spoolDir   = $this->getSpoolDir();
        $filesystem = new Filesystem();
        $message    = false;

        if ($filesystem->exists($spoolDir)) {
            $finder = new Finder();
            $finder->in($spoolDir)->ignoreDotFiles(true)->files();

            foreach ($finder as $file) {
                /** @var Swift_Message $message */
                $tmpMessage = unserialize(file_get_contents($file));

                $headers = $tmpMessage->getHeaders();
                if ($headers->has('X-Message-ID')) {
                    if ($headers->get('X-Message-ID')->getValue() == $messageId) {
                        $message = $tmpMessage;
                    }
                }
            }
        }

        return $message;
    }

    /**
     * We need to purge the spool between each scenario
     *
     * @BeforeScenario
     */
    public function purgeSpool()
    {
        $spoolDir = $this->getSpoolDir();

        $filesystem = new Filesystem();
        $filesystem->remove($spoolDir);
    }

    /**
     * @return string
     */
    private function getSpoolDir()
    {
        return $this->kernel->getContainer()->getParameter('swiftmailer.spool.default.file.path');
    }

    private function replaceParams($content)
    {
        echo $contentUpdated = preg_replace_callback(
            '/{{(.+)}}/',
            function ($matches) {
                return $this->kernel->getContainer()->getParameter($matches[1]);
            },
            $content
        );

        return $contentUpdated;
    }
}
