<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel;

/**
 * Value object used to hold the current state of the frameworks environment 
 * settings.
 */
class KernelState implements KernelStateInterface
{
	/**
	 * Include path string
	 * @var string
	 */
	protected $includePath = null;
	
	/**
	 * Value used to turn display_errors on or off
	 * @var string
	 */
	protected $displayError = null;
	
	/**
	 * Level used in error reporting. This is the same codes used in the config
	 * @var string
	 */
	protected $errorReporting = null;

	/**
	 * @var string
	 */	
	protected $defaultTimezone = null;

	/**
	 * @var bool
	 */
	protected $isAutoloadEnabled = false;	
	
	/**
	 * State of the autoload stack
	 * @var array
	 */
	protected $autoloadStack = null;

	/**
	 * pull all the settings togather to determine the state of the env
	 * @return	State
	 */
	public function __construct()
	{
		$this->displayError    = ini_get('display_errors');
		$this->errorReporting  = error_reporting();
		$this->defaultTimezone = date_default_timezone_get();
		$this->includePath	   = get_include_path();
		
		$stack  = spl_autoload_functions();
		$this->autoloadStack = $stack;
		$this->isAutoloadEnabled = is_array($stack) && ! empty($stack);
	}

	/**
	 * @return string
	 */
	public function getDisplayError()
	{
		return $this->displayError;
	}

	/**
	 * @return string
	 */
	public function getErrorReporting()
	{
		return $this->errorReporting;
	}

	/**
	 * @return string
	 */
	public function getDefaultTimezone()
	{
		return $this->defaultTimezone;
	}

	/**
	 * @return string
	 */
	public function getIncludePath()
	{
		return $this->includePath;
	}

	/**
	 * @return bool
	 */
	public function isAutoloadEnabled()
	{
		return $this->isAutoloadEnabled;
	}

	/**
	 * @return	bool
	 */
	public function getAutoloadStack()
	{
		return $this->autoloadStack;
	}
}
