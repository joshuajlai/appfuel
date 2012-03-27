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
		$dir     = "fuelcell/web/app-view";
		$tpl     = "$dir/welcome/welcome-view.phtml";
		$init    = "$dir/welcome/welcome-init.pjs";
		$htmlDoc = "$dir/html-doc/doc.phtml";
		$config  = "$dir/html-doc/default-config.php";
        $primary = array(
            'is-public'	  => true,
            'action-name' => 'WelcomeAction',
            
			'view-detail' => array(
				'is-view'  => true,
				'strategy' => 'html-page',
				'params'   => array(
					'html-doc'			 => $htmlDoc,
					'html-config'		 => $config,
					'view-template'		 => $tpl,
					'inline-js-template' => $init
				),
			),
        );   

		/* the empty route will points to the welcome route */
        $alternates = array('' => false);
    
        parent::__construct('welcome', $primary, $alternates);
    }
}
