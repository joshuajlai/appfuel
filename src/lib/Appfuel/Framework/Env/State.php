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
namespace Appfuel\Framework\Env;

use Appfuel\Framework\Exception,
	Appfuel\Stdlib\Data\BagInterface,
	Appfuel\Stdlib\Data\Bag;

/**
 * Value object used to hold the current state of the frameworks environment 
 * settings.
 */
class State
{
	/**
	 * Include path string
	 * @var string
	 */
	protected $includePath = null;
	
	/**
	 * The action used on the orignal include path to get the include path.
	 * Did the include path replace, append, or prepend the orignal path
	 * @var string
	 */
	protected $includePathAction = null;

	/**
	 * Value used to turn display_errors on or off
	 * @var string
	 */
	protected $errorDisplay = null;
	
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
	 * @return bool
	 */
	public function isErrorDisplay()
	{
		return null !== $this->errorDisplay;
	}

	/**
	 * @param	string	$value
	 * @return	State
	 */
	public function setErrorDisplay($value)
	{
		$this->errorDisplay = $value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getErrorDisplay()
	{
		return $this->errorDisplay;
	}

	/**
	 * @return bool
	 */
	public function isErrorReporting()
	{
		return null !== $this->errorReporting;
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
	public function setErrorReporting($codes)
	{
		$this->errorReporting = $codes;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDefaultTimezone()
	{
		return null !== $this->defaultTimezone;
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
	public function setDefaultTimezone($zone)
	{
		$this->defaultTimezone = $zone;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isIncludePath()
	{
		return null !== $this->includePath;
	}

	/**
	 * @return string
	 */
	public function getIncludePath()
	{
		return $this->includePath;
	}

	/**
	 * @return string
	 */
	public function setIncludePath($path)
	{
		$this->includePath = $path;
		return $this;
	}

	/**
	 * @return 
	 */
	public function getIncludePathAction()
	{
		return $this->includePathAction;
	}

	/**
	 * @param	string	$action
	 * @return	State
	 */
	public function setIncludePathAction($action)
	{
		$this->includePathAction = $action;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAutoloadEnabled()
	{
		return $this->isAutoloadEnabled;
	}

	/**
	 * @return bool
	 */
	public function isAutoloadStack()
	{
		return null !== $this->autoloadStack;
	}

	/**
	 * @param	bool $flag
	 * @return	State
	 */
	public function setEnableAutoload($flag)
	{
		$this->isAutoloadEnabled =(bool) $flag;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function getAutoloadStack()
	{
		return $this->autoloadStack;
	}

	/**
	 * @return Stack
	 */
	public function setAutoloadStack($stack)
	{
		$this->autoloadStack = $stack;
		return $this;
	}
}
