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
namespace Appfuel\Kernel\Mvc;

use Appfuel\Error\ErrorStackInterface;

/**
 * The primary reposiblity is to provide a fluent interface that can build
 * a context in any configuration. It does the by using a context builder, 
 * which in my own implementation I inject in the constructor and provide no
 * public getter or setter.The dispatcher provides two interface for 
 * dispatching: dispatch uses the context builder and fluent interface and
 *              runDispatch which is completely manual way to dispatch the
 *				context.
 */
interface MvcActionDispatcherInterface
{
	/**
	 * Used to determine which method in the mvc action will be used to 
	 * process the context
	 *
	 * @param	string	$strategy
	 * @return	MvcActionDispatcherInterface
	 */
	public function setStrategy($strategy);

	/**
	 * @param	string
	 */
	public function getStrategy();
	
	/**
	 * Manually determine the route key to use in dispatching
	 *
	 * @param	string $route
	 * @return	MvcActionDispatcherInterface
	 */
	public function setRoute($route);

	/**
	 * @return	string
	 */
	public function getRoute();

	/**
	 * Manual set the RequestUri by passing in a string (context builder will 
	 * create it) or an object using the correct interface
	 *
	 * @param	RequestUriInterface $uri
	 * @return	MvcActionDispatcherInterface
	 */
	public function setUri($uri);

	/**
	 * Generates an RequestUri using the super global $_SERVER['REQUEST_URI']
	 * as the uri string
	 *
	 * @return	MvcActionDispatcherInterface
	 */
	public function useServerRequestUri();

	/**
	 * @param	string	$code
	 * @return	MvcActionDispatcherInterface
	 */
	public function addAclCode($code);

	/**
	 * @param	array $codes
	 * @return	MvcActionDispatcherInterface
	 */	
	public function addAclCodes(array $codes);

	/**
	 * This will allow you to manual define the input used in the context 
	 * that will be dispatched. If a uri has also been defined then its 
	 * parameters will be used as the inputs get parameters by default. If
	 * you already have get parameters then the uri params will be merged
	 *
	 * @param	string	$method	 get|post or cli
	 * @param	array	$params	 input parameters
	 * @param	bool	$useUri  flag used to determine if the get parameters
	 *							 will be obtained from the uri
	 * @return	MvcActionDispatcherInterface
	 */
	public function defineInput($method, array $params, $useUri = true);

	/**
	 * This will create inputs from the php super globals $_POST, $_FILES,
	 * $_COOKIE and $_SERVER['argv']. If useUri is true the get params will
	 * be used from the uri otherwise if you $_GET you will have to manual
	 * define it your self
	 * 
	 * @return	MvcActionDispatcherInterface
	 */
	public function defineInputFromSuperGlobals($useUri = true);

	/**
	 * This will use all the parameters in the uri object for the get params
	 * in the input object and set the input method to 'get'
	 *
	 * @return	MvcActionDispatcherInterface
	 */
	public function useUriForInputSource();
	
	/**
	 * @return	AppContextInterface
	 */
	public function buildContext();

	/**
	 * Run the dispatch without any use of the fluent interface
	 *
	 * @param	AppContextInterface $context
	 * @return	AppContextInterface
	 */
	public function dispatch(MvcContextInterface $context);
}
