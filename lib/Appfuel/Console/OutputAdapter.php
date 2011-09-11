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


use Appfuel\Framework\Exception,
	Appfuel\Framework\Output\EngineAdapterInterface;

/**
 * Handle specific details for outputting data to the commandline
 */
class ConsoleOutputAdapter implements EngineAdapterInterface
{
	/**
	 * @param	string	$format	
	 * @return	bool
	 */
	public function isFormatSupported($format)
	{
		if (empty($format) || ! is_string($format)) {
			return false;
		}

		$format = strtolower($format);
		$supported = array('text', 'json', 'csv');
		if (in_array($format, $supported)) {
			return true;
		}

		return false;
	}

	/**
	 * Render to the command line or build into a string
	 * 
	 * @param	mixed	$data
	 * @param	string	$strategy
	 * @return	mixed
	 */
	public function output($data, $strategy = 'render')
	{
		if ('render' === $strategy) {
			$result = $this->render();
		} else {
			$result = $this->build();
		}

		return $result;
	}

	/**
	 * @param	mixed	$data
	 * @return	string
	 */
	public function build($data)
	{
		if (! $this->isValidOutput($data)) {
			return '';
		}

		if (is_scalar($data)) {
			return $data;
		}

		return $data->__toString();
	}

	/**
	 * @param	mixed	$data
	 * @return	bool
	 */
	public function isValidOutput($data)
	{
		if (is_scalar($data) || 
			is_object($data) && method_exists($data, '__toString')) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$format
	 * @return	null
	 */
	public function renderFormatNotSupportedError($format)
	{
		$format =(string) $format;
		$error ="Console ouput error: format -($format) is not supported";
		$this->renderError($error);
	}

	/**
	 * Render output to the stdout.
	 * 
	 * @param	mixed	$data
	 * @return	null
	 */
	public function render($data)
	{
		if (! $this->isValidOutput($data)) {
			$err  = 'Error: object to render is not a string or does ';
			$err .= 'not support __toString';
			$this->renderError($err);
			return;
		}

		if (is_object($data)) {
			$data = $data->__toString();
		}

		fwrite(STDOUT, $data);
	}

	/**
	 * @param	string	$msg	error message
	 * @return	null
	 */
	public function renderError($msg)
	{
		$msg =(string) $msg;
		fwrite(STDERR, "$msg\n");
	}
}
