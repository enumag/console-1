<?php

namespace Tests\Fixtures;

use Contributte\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class FoobarCommand extends AbstractCommand
{

	/** @var BazClass */
	private $dep1;

	/**
	 * @param BazClass $dep1
	 */
	public function __construct(BazClass $dep1)
	{
		parent::__construct(NULL);
		$this->dep1 = $dep1;
	}

	/**
	 * Configure command
	 *
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('foobar');
		$this->setDescription('That is awesome!');
		$this->setHelp('Ugly help');

		$this->addUsage('First of all');
		$this->addUsage('Second one');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->write('OK');
	}

}
