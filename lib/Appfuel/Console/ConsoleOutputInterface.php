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

/**
 * Provides validation to ensure scalar data or objects that implement
 * __toString. Will render to the standard output stream and will render
 * errors to the standard error stream
 */
interface ConsoleOutputInterface
{
	/**
	 * @param	mixed	$data
	 * @return	bool
	 */
	public function isValidOutput($data);

	/**
	 * Write to the STDOUT.
	 *
	 * @param	mixed	$data
	 * @return	null
	 */
	public function render($data);

	/**
	 * Write to the STDERR.
	 *
	 * @param	string	$msg	error message
	 * @return	null
	 */
	public function renderError($data);
}
