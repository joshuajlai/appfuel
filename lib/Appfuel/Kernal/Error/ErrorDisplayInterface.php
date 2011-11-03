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
 * Normalize all possible values for representing on, off and standard error
 * to just 'on', 'off' and 'stderr'. Use those values to set the ini 
 * directive display_errors. Also provide functionality to display the current
 * status of ini_get('display_errors').
 */
interface ErrorDisplayInterface
{
    /**
     * @return  bool
     */
    public function enable();

    /**
     * @return  bool
     */
    public function disable();

    /**
     * @return  bool
     */
    public function sendToStdError();

	/**
	 * Normalize all valid values into three standard options 
	 * 'on', 'off', 'stderr'
	 *
	 * @param	string	$value
	 * @return	mixed	string|false
	 */
	public function getValue($value);

    /**
	 * Normailze the values so they are always 'on', 'off' or 'stderr'
	 * then use that value to set the ini directive
	 *
	 * @param	string	$value
	 * @return  bool
     */
    public function set($value);

    /**
	 * Should return the value of call to ini_get('display_errors')
     * @return  string
     */
    public function get();
}
