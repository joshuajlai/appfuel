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
namespace TestFuel\Functional\Action\TestRouteManager;

use Appfuel\Kernel\Mvc\MvcRouteHandler;

/**
 * Test the RouteManager can create the route handler class
 */
class RouteHandler extends MvcRouteHandler
{
	/**
	 * setup route detail configurations
	 */
	public function __construct()
	{
		$detail = array(
			'is-public'		=> true,
			'is-internal'	=> false,	
		);

		/* all aliases use the main route detail */
		$alias = array(
			'alias-a' => false,
			'alias-b' => false,
			'alias-c' => false
		);
		parent::__construct('my-route', $detail, $alias);
	}
}
