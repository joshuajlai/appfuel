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

use Appfuel\Kernel\OutputInterface;

/**
 * The front controller is used build the intialize context, run the pre
 * intercepting filters, dispatch to the mv action, handle any errors,
 * run post filters and output the results.
 */
interface MvcFrontInterface
{
    /**
     * @return  MvcActionDispatcherInterface
     */
    public function getDispatcher();

    /**
     * @param   string  $strategy   ajax|html|console
     * @return  MvcFrontInterface
     */
    public function setStrategy($strategy);

    /**
     * @param   string  $route
     * @return  MvcFrontInterface
     */
    public function setRoute($route);
	
    /**
     * @param   array   $codes
     * @return  MvcFrontInterface
     */
    public function addAclCodes(array $codes);

    /**
     * @param   string  $code
     * @return  MvcFrontInterface
     */
    public function addAclCode($code);

    /**
     * @param   OutputInterface $output
     * @return  MvcFront
     */
    public function setOutputEngine(OutputInterface $output);

    /**
     * @return  OutputInterface
     */
    public function getOutputEngine();

    /**
     * @param   mixed   string|RequestUriInterface
     * @return  MvcFrontInterface
     */
    public function setUri($uri);

    /**
     * @return  MvcFrontInterface
     */
    public function useServerRequestUri();

	/**
	 * @param	string	$method	 get|post|cli
	 * @param	array	$params
	 * @param	bool	$useUri  use the uri for get parameters 
	 * @return	MvcFrontInterface
	 */
    public function defineInput($method, array $params, $useUri = true);

    /**
     * @param   bool    $useUri 
     * @return  MvcFrontInterface
     */
    public function defineInputFromSuperGlobals($useUri = true);

    /**
     * @return  MvcFrontInterface
     */
    public function useUriForInputSource();

    /**
     * @return  MvcFront
     */
    public function noInputRequired();

    /**
	 * This method should set the strategy to 'console', set the uri to the
	 * parameter passed in and use inputs defined by super globals
	 *
     * @param   string  $route
     * @param   string|RequestUriInterface
     * @return  int		this is the context exit code (http status code)
     */
    public function runConsoleUri($uri);

    /**
     * This will dispatch a route with the console strategy and define its
     * inputs from the super global which means $_SERVER['argv']
     *
     * @param   string  $route
     * @return  int 
     */
    public function runConsoleRoute($route);

	/**
	 * @return	int
	 */
	public function runAjax();

	/**
	 * @return	int
	 */
	public function runHtml();
	
	/**
	 * @param	string	$strategy
	 * @return	int
	 */
	public function run(MvcActionDispatcherInterface $dispatcher);	
}
