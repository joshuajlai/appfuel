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
namespace Appfuel\Kernal\Error;

/**
 * Defined functionality aimed at easier usage of error_reporting function
 *
 *	'none'               => 0,
 *	'error'              => E_ERROR,
 *	'warning'            => E_WARNING,
 *	'parse'              => E_PARSE,
 *	'notice'             => E_NOTICE,
 *	'core_error'         => E_CORE_ERROR,
 *	'core_warning'       => E_CORE_WARNING,
 *	'compile_error'      => E_COMPILE_ERROR,
 *	'compile_warning'    => E_COMPILE_WARNING,
 *	'user_error'         => E_USER_ERROR,
 *	'user_warning'       => E_USER_WARNING,
 *	'user_notice'        => E_USER_NOTICE,
 *  'strict'             => E_STRICT,
 *	'recoverable_error'  => E_RECOVERABLE_ERROR,
 *	'deprecated'         => E_DEPRECATED,
 *	'user_deprecated'    => E_USER_DEPRECATED,
 *	'all'                => E_ALL,
 * 
 * The usage example of strings:
 *	setLevel('error, warning, notice')	should be E_ALL|E_WARNING|E_NOTICE
 *  setLevel('all, -warning, -notice)	should be E_ALL & ~(E_WARNING|E_NOTICE)
 * 
 */
interface ErrorLevelInterface
{
	/**
	 * Enable all error levels
	 * @return	null
	 */
	public function enableAll();

	/**
	 * Disable all error levels
	 * @return null
	 */
	public function disableAll();

    /**
	 * Wraps the functionality of error_reporting. Excepts a comma separated
	 * string of codes that correspond to the php error constants. If -1 is
	 * is passed in then all codes will be used and if 0 is passed in then 
	 * all codes will be disabled. When a code in the comma separated list
	 * is prefixed with a dash then that code is treated as one to be disabled.
	 *
	 * @param	string	$codes	comma separated string to be included
	 * @return	null
     */
    public function setLevel($codes);

    /**
     * Map the current reporting level into an array of enabled and disabled
	 * levels
	 *
     * @return  array
     */
    public function getLevel();

    /**
     * @param   int $errorLevel     
     * @return  FALSE|string
     */
    public function mapLevel($errorLevel);

    /**
     * Returns the PHP Constant for the given code if mapped
     *
	 * @param   string  $code 
     * @return  FALSE|int
     */
    public function mapCode($code);

    /**
     * Determins if a given error code exists in the map
     *
	 * @param   string  $code
     * @return  bool
     */
    public function isCode($code);

	/**
	 * @return array
	 */
	public function getMap();
}
