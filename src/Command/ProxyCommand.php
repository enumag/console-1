<?php

namespace Contributte\Console\Command;

use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class ProxyCommand extends AbstractCommand
{

	/** @var string */
	private $class;

	/** @var callable */
	private $factory;

	/** @var Command */
	private $proxy;

	/**
	 * @param string $class
	 * @param callable $factory
	 */
	public function __construct($class, callable $factory)
	{
		$this->class = $class;
		$this->factory = $factory;

		parent::__construct('proxy');
	}

	/**
	 * PROXY *******************************************************************
	 */

	/**
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments = [])
	{
		$proxy = $this->getProxy();
		$ret = call_user_func_array([$proxy, $name], $arguments);

		return $ret;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->__call(__FUNCTION__);
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->__call(__FUNCTION__);
	}

	/**
	 * @return HelperSet
	 */
	public function getHelperSet()
	{
		return $this->__call(__FUNCTION__);
	}

	/**
	 * @param HelperSet $helperSet
	 * @return void
	 */
	public function setHelperSet(HelperSet $helperSet)
	{
		parent::setHelperSet($helperSet);
		$this->__call(__FUNCTION__, [$helperSet]);
	}

	/**
	 * @return array
	 */
	public function getAliases()
	{
		return $this->__call(__FUNCTION__);
	}

	/**
	 * @return string
	 */
	public function getHelp()
	{
		return $this->__call(__FUNCTION__);
	}

	/**
	 * @return array
	 */
	public function getUsages()
	{
		return $this->__call(__FUNCTION__);
	}

	/**
	 * @param bool $short
	 * @return string
	 */
	public function getSynopsis($short = FALSE)
	{
		return $this->__call(__FUNCTION__, [$short]);
	}

	/**
	 * @return void
	 */
	protected function configure()
	{
		$proxy = $this->getProxy();

		$rf = new ReflectionClass($this->class);
		$rm = $rf->getMethod('configure');
		$rm->setAccessible(TRUE);
		$rm->invoke($proxy);

		$this->setName($proxy->getName());
		$proxy->setDefinition($this->getDefinition());
	}

	/**
	 * @return Command
	 */
	protected function getProxy()
	{
		if (!$this->proxy) {
			$rf = new ReflectionClass($this->class);
			$this->proxy = $rf->newInstanceWithoutConstructor();
		}

		return $this->proxy;
	}

	/**
	 * EXECUTING ***************************************************************
	 */

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/** @var Command $command */
		$command = call_user_func($this->factory);
		$command->setApplication($this->getApplication());
		$command->execute($input, $output);
	}

}
