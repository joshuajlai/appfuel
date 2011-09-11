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
namespace Appfuel\Output;


use Appfuel\Framework\Exception,
	Appfuel\Framework\Output\EngineAdapterInterface;

/**
 * Handle specific details for outputting data to the commandline
 */
class CliAdapter implements EngineAdapterInterface
{
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
			fwrite(STDERR, "$err\n");
			return;
		}

		if (is_object($data)) {
			$data = $data->__toString();
		}

		fwrite(STDOUT, $data);
	}
}
