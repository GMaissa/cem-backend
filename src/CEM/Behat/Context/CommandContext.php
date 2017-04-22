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

use CEM\Ui\ConsoleBundle\Command\UpdateVmStateCommand;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * OAuth Context
 */
class CommandContext implements KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var CommandTester
     */
    private $tester;

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernelInterface)
    {
        $this->kernel = $kernelInterface;
    }

    /**
     * @When I run the :command command
     */
    public function iRunTheCommand($command)
    {
        $this->iRunTheCommandWithOptions($command);
    }

    /**
     * @param TableNode $tableNode
     *
     * @When I run the :command command with options:
     */
    public function iRunTheCommandWithOptions($command, TableNode $tableNode = null)
    {
        $cmd = $this->getApplication()->find($command);
        $options = ($tableNode !== null) ? $this->setOptions($tableNode) : [];

        $this->getTester($cmd)->execute($options);
    }

    /**
     * @param PyStringNode $output
     *
     * @Then the command output should be:
     */
    public function theCommandOutputShouldBe(PyStringNode $output)
    {
        $current = trim($this->getTester()->getDisplay());
        if ($current != $output->getRaw()) {
            throw new \LogicException(sprintf('Current output is: [%s]', $current));
        }
    }

    /**
     * Retrieve the console application
     *
     * @return Application
     */
    private function getApplication()
    {
        if ($this->application === null) {
            $this->application = new Application($this->kernel);
            $this->application->add(new UpdateVmStateCommand());
        }

        return $this->application;
    }

    /**
     * Retrive the command tester object
     *
     * @param Command $command
     *
     * @return CommandTester
     */
    private function getTester(Command $command = null)
    {
        if ($this->tester === null && !is_null($command)) {
            $this->tester = new CommandTester($command);
        }

        return $this->tester;
    }

    /**
     * Structure command options from step params
     *
     * @param TableNode $tableNode
     *
     * @return array
     */
    private function setOptions(TableNode $tableNode)
    {
        $options = [];
        foreach ($tableNode->getRowsHash() as $key => $value) {
            $options[$key] = $value;
        }

        return $options;
    }
}
