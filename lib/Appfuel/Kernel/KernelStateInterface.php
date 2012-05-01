<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel;

/**
 * Value object used to hold the current state of the frameworks environment 
 * settings.
 */
interface KernelStateInterface
{
	/**
	 * @return string
	 */
	public function getDisplayError();

	/**
	 * @return string
	 */
	public function getErrorReporting();

	/**
	 * @return string
	 */
	public function getDefaultTimezone();

	/**
	 * @return string
	 */
	public function getIncludePath();

	/**
	 * @return bool
	 */
	public function isAutoloadEnabled();

	/**
	 * @return	bool
	 */
	public function getAutoloadStack();
}
