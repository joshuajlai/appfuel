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
namespace TestFuel\Functional\Action\Welcome;

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
		$primary = array(
			'is-public'		=> true,
			'is-internal'	=> false,	
		);

		/* all aliases use the main route detail */
		$alternates = array(
			'welecome-a'  => false,
			'welecome-b'  => false
			'not-welcome' => array(
				'is-public' => false,
				'acl-access' => array('app-admin', 'app-dev'),
			),
		);
		parent::__construct('welcome', $primary, $alternates);
	}
}
