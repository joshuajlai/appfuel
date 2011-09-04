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
namespace Appfuel\Framework\App\Context;

/**
 * Represents functionality to handle retrieving user input.
 */
interface ContextInputInterface
{
    /**
     * @return bool
     */
    public function isPost();

    /**
     * @return string
     */
    public function isGet();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * Retreive a parameter based on its type. When the parameter key is not
	 * found the default value is given instead. Parameters are seperated based
	 * on type. The following are the types:
	 *
	 * get	  - http get. app usually parses with PrettyUri
	 * post   - http post. generally retrieved from $_POST
	 * files  - for file uploads. generally retrieved from $_FILES
	 * cookie - cooke/session. generally retrieved from $_COOKIE
	 * argv   - command line args. generally retrieved from $_SERVER['argv']
	 *	
     * @param   string  $type       (get|post|files|cookie|argv)
     * @param   string  $key        used to find the label
     * @param   mixed   $default    value returned when key is not found
     * @return  mixed
     */
	public function get($type, $key, $default = null);

    /**
	 * Return all the parameters for a particular type. When the type is null
	 * return the entire parameter list
	 *
     * @param   string  $type
     * @return  array
     */
    public function getAll($type = null);

}
