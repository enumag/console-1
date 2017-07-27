<?php

/**
 * Test: Command\AbstractCommand.Proxy
 */

use Contributte\Console\DI\ConsoleExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Tester\Assert;
use Tester\FileMock;
use Tests\Fixtures\TestOutput;

require_once __DIR__ . '/../../bootstrap.php';

// Test lazy loading
test(function () {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler) {
		$compiler->addExtension('console', new ConsoleExtension());
		$compiler->loadConfig(FileMock::create('
		services:
			baz: Tests\Fixtures\BazClass
			- Tests\Fixtures\FoobarCommand
		', 'neon'));
	}, [microtime(), 1]);

	/** @var Container $container */
	$container = new $class;

	/** @var Application $application */
	$application = $container->getByType(Application::class);
	Assert::false($container->isCreated('baz'));

	$output = new TestOutput();
	$application->setAutoExit(FALSE);
	$application->run(new StringInput('list'), $output);

	Assert::false($container->isCreated('baz'));
	Assert::equal(<<<CONSOLE
Console Tool

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  foobar  That is awesome!
  help    Displays help for a command
  list    Lists commands

CONSOLE
		, $output->output);
});

// Test running command
test(function () {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler) {
		$compiler->addExtension('console', new ConsoleExtension());
		$compiler->loadConfig(FileMock::create('
		services:
			baz: Tests\Fixtures\BazClass
			- Tests\Fixtures\FoobarCommand
		', 'neon'));
	}, [microtime(), 2]);

	/** @var Container $container */
	$container = new $class;

	/** @var Application $application */
	$application = $container->getByType(Application::class);

	Assert::false($container->isCreated('baz'));

	$output = new TestOutput();
	$application->setAutoExit(FALSE);
	$application->run(new StringInput('foobar'), $output);

	Assert::true($container->isCreated('baz'));
	Assert::equal('OK', $output->output);
});

// Test running help of command
test(function () {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler) {
		$compiler->addExtension('console', new ConsoleExtension());
		$compiler->loadConfig(FileMock::create('
		services:
			baz: Tests\Fixtures\BazClass
			- Tests\Fixtures\FoobarCommand
		', 'neon'));
	}, [microtime(), 3]);

	/** @var Container $container */
	$container = new $class;

	/** @var Application $application */
	$application = $container->getByType(Application::class);
	Assert::false($container->isCreated('baz'));

	$output = new TestOutput();
	$application->setAutoExit(FALSE);
	$application->run(new StringInput('foobar -h'), $output);

	Assert::false($container->isCreated('baz'));
	Assert::equal(<<<CONSOLE
Usage:
  foobar
  foobar First of all
  foobar Second one

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  Ugly help

CONSOLE
, $output->output);
});
