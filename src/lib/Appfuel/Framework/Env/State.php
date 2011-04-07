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

/**
 * Value object used to hold the current state of the frameworks environment settings.
 */
class State
{
	/**
	 * Indicates if errors are displayed 
	 * @return string
	 */
	protected $displayErrors  = null;
	
	/**
	 * Indicates the current level of error reporting. Uses the frameworks
	 * mapped values not the constant integer
	 * @var string
	 */
	protected $errorReporting = null;

	/**
	 * Indicated the frameworks current timezone settings
	 * @var string
	 */
	protected $timezone = null;

	/**
	 * The autoload stack used to autoloading
	 * @var array
	 */
	protected $autoloadStack = array();
	
	/**
	 * The frameworks includepath
	 * @var string
	 */
	protected $includePath = null;


	/**
	 * @param	string	$eshow		setting for display errors
	 * @param	string	$ereport	setting for error reporting
	 * @param	string	$tz			setting for default timzezone
	 * @param	array	$loaders	autoloader stack
	 * @param	string	$paths		include paths
	 * 
	 * @return	State
	 */
	public function __construct($eshow, $ereport, $tz, array $loaders, $path)
	{
		$this->displayErrors  = $eshow;
		$this->errorReporting = $ereport;
		$this->timezone       = $tz;
		$this->autoloadStack  = $loaders;
		$this->includePath	  = $path;
	}

	/**
	 * @return string
	 */
	public function displayErrors()
	{
		return $this->displayErrors;
	}

	/**
	 * @return string
	 */
	public function errorReporting()
	{
		return $this->errorReporting;
	}

	/**
	 * @return string
	 */
	public function defaultTimezone()
	{
		return $this->timezone;
	}

	/**
	 * @return array
	 */
	public function autoloadStack()
	{
		return $this->autoloadStack;
	}

	/**
	 * @return string
	 */
	public function includePath()
	{
		return $this->includePath;
	}
}
