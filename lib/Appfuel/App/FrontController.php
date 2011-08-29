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
namespace Appfuel\App;


use Appfuel\Framework\Exception,
	Appfuel\Framework\App\FrontControllerInterface,
	Appfuel\Framework\App\Action\ControllerInterface,
	Appfuel\Framework\App\Action\ActionBuilderInterface,
	Appfuel\Framework\App\Route\RouteInterface,
    Appfuel\Framework\App\ContextInterface,
    Appfuel\Framework\View\DocumentInterface,
	Appfuel\App\Route\ErrorRoute;

/**
 * Handle dispatching the request and outputting the response
 */
class FrontController implements FrontControllerInterface
{
	/**
	 * Manage Intercept pre and post filters
	 * @var FilterManagerInterface
	 */
	protected $filterManager = null;

	/**
	 * When so controller is passed in create the default error controller
	 *
	 * @param	ActionInterface		$controller
	 * @return	FrontController
	 */
	public function __construct()
	{
	}

    /**
     * @param   MessageInterface $msg
     * @return  MessageInterface
     */
    public function dispatch(ContextInterface $context)
    {
        return $context;
    }
	
	/**
	 * @param	RouteInterface $route
	 * @param	string		   $responseType
	 * @return	ControllerInterface
	 */
	public function buildController(ContextInterface $context)
	{
	}

	/**
	 * See interface for details
	 *
	 * @param	RouteInterface	$route 
	 * @return	ActionBuilder	
	 */
	public function createActionBuilder(RouteInterface $route)
	{
	}

	/**
	 * See interface for details
	 *
	 * @param	ControllerInterface	$controller
	 * @param	MessageInterface	$msg	
	 * @return  MessageInterface		
	 */
	public function execute(ControllerInterface $ctr, ContextInterface $con)
	{
		return $con;
	}
}
