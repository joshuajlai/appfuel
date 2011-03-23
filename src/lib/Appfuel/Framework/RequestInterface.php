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
namespace Appfuel\Framework;

/**
 * Common logic to handle requests given to the application from any type.
 * Different types include web requests, cli request and api requests.
 * The request provides a common interface that all application types can
 * follow.
 */
interface RequestInterface
{
    /**
     * Assign the uri, parameters and request method. Because the uri contains
	 * all the get parameters we pull them out and add them to the
	 * the others (post, cookie, files) which are passed into the constructor.
	 * We also look for additional get params and merge them as required.
	 *
	 * @param	Uri		$uri
	 * @param	array	$params		holds post, files, cookie parameters
	 * @param	string	$rm			request method
     * @return	Request
     */
    public function __construct(Uri $uri, array $params = array(), $rm = 'get');

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
    public function getRequestMethod();

	/**
	 * @return string
	 */
	public function getUriString();

	/**
	 * @return	string
	 */
	public function getRouteString();

	/**
	 * @return	string
	 */
	public function getParamString();

    /**
     * The params member is a general array that holds any or all of the
     * parameters for this request. This method will search on a particular
     * parameter and return its value if it exists or return the given default
     * if it does not
     *
     * @param   string  $key        used to find the label
     * @param   mixed   $default    value returned when key is not found
     * @param   string  $type       type of parameter get, post, cookie etc
     * @return  mixed
     */
	public function getParam($key, $default = NULL, $type = 'get');

    /**
     * @param   string  $type
     * @return  array
     */
    public function getAll($type);

}
