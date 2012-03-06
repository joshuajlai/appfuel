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
namespace FuelCell\Action\Welcome;

use Appfuel\Kernel\Mvc\MvcRouteHandler;

/**
 * Appfuel welcome page
 */
class RouteHandler extends MvcRouteHandler
{
    /**
     * @return  RouteHandler
     */
    public function __construct()
    {
		$pkgDir = 'fuelcell/html/';
		$welcomeDir = "{$pkgDir}welcome/";
        $primary = array(
            'is-public'	  => true,
            'action-name' => 'WelcomeAction',
            
			'view-detail' => array(
				'is-view'  => true,
				'strategy' => 'html-page',
				'params'   => array(
					'html-doc'			 => "{$pkgDir}doc/htmldoc.phtml",
					'html-config'		 => "{$pkgDir}doc/default.php",
					'view-template'		 => "{$welcomeDir}welcome-view.phtml",
					'inline-js-template' => "{$welcomeDir}welcome-init.pjs"
				),
			),
        );   

		/* the empty route will points to the welcome route */
        $alternates = array('' => false);
    
        parent::__construct('welcome', $primary, $alternates);
    }
}
