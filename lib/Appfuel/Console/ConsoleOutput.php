<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Console;


use InvalidArgumentException,
	Appfuel\Kernel\OutputInterface;

/**
 * Provides validation to ensure scalar data or objects that implement
 * __toString. Will render to the standard output stream and will render
 * errors to the standard error stream
 */
class ConsoleOutput implements OutputInterface
{
	/**
	 * @param	mixed	$data
	 * @return	bool
	 */
	public function isValidOutput($data)
	{
		if (is_scalar($data) || 
			is_object($data) && is_callable(array($data, '__toString'))) {
			return true;
		}

		return false;
	}

	/**
	 * Its the Output engines responsiblity to validate the output is 
	 * is safe to use.
	 * 
	 * @param	mixed	$data
	 * @return	null
	 */
	public function render($data)
	{
		if (! $this->isValidOutput($data)) {
			$err = 'data must be able to cast to a string';
			throw new InvalidArgumentException($err);
		}

		fwrite(STDOUT, (string)$data);
	}

	/**
	 * @param	string	$msg	error message
	 * @paraj	int		$code	ignored by commandline
	 * @return	null
	 */
	public function renderError($msg, $code = 1)
	{
		if (! $this->isValidOutput($msg)) {
			$err = 'data must be able to cast to a string';
			throw new InvalidArgumentException($err);
		}

		fwrite(STDERR, (string)$msg . PHP_EOL);
	}
}
