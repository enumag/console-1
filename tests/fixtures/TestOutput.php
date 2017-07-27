<?php

namespace Tests\Fixtures;

use Symfony\Component\Console\Output\Output;

class TestOutput extends Output
{

	/** @var string */
	public $output = '';

	/**
	 * Clear output
	 *
	 * @return void
	 */
	public function clear()
	{
		$this->output = '';
	}

	/**
	 * @param string $message
	 * @param bool $newline
	 * @return void
	 */
	protected function doWrite($message, $newline)
	{
		$this->output .= $message . ($newline ? "\n" : '');
	}

}
