<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\App;

/**
 * Represents functionality to handle retrieving user input. There is two 
 * categories of input: http and cli. They have been delibrately coupled 
 * into the AppInputInterface to allow either one to mock the other. Thus it
 * is possible to write code easily to fake a http post from the cli.
 */
interface AppInputInterface
{
    /**
     * @return bool
     */
    public function isPost();

    /**
     * @return bool
     */
    public function isGet();
 
    /**
     * @return bool
     */
    public function isPut();

   /**
     * @return bool
     */
    public function isDelete();

	/**
	 * @return bool
	 */
	public function isCli();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * Used only with command line input. Gets the command name that was used
     * on the commandline
     *
     * @return  string | false
     */
    public function getCmd();

    /**
     * @param   string  $opt
     * @return  bool
     */
    public function isShortOptFlag($opt);
    
    /**
     * @param   string  $opt
     * @param   mixed   $default
     * @return  mixed
     */
    public function getShortOpt($opt, $default = null);

    /**
     * @param   string  $opt
     * @return  bool
     */
    public function isLongOptFlag($opt);

    /**
     * @param   string  $opt
     * @param   mixed   $default
     * @return  mixed
     */
    public function getLongOpt($opt, $default = null);

    /**
     * @return  array
     */
    public function getArgs();

	/**
     * Should call getMethod then delegate to AppInput::get
     *
     * @param   string  $key 
     * @param   mixed   $default
     * @return  mixed
     */
    public function getParam($key, $default = null);

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
     * @param   string  $type       (get|post|files|cookie|argv|app)
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

	/**
	 * @param	$bool	returnString
	 * @return	string|int|false
	 */
	public function getIp($isInt = true);

}
