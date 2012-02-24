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
namespace Appfuel\FuelCell\Action\Welcome;

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
		$tplDir = 'appfuel/html/tpl';
		$docTpl = "$tplDir/doc/htmldoc.phtml";
		$config = "$tplDir/doc/default.php";
		$view   = "$tplDir/view/welcome/welcome-view.phtml";

        $primary = array(
            'is-public'	  => true,
            'action-name' => 'WelcomeAction',
            
			'view-detail' => array(
				'is-view'  => true,
				'strategy' => 'html-page',
				'params'   => array(
					'html-doc'	    => $docTpl,
					'html-config'   => $config,
					'view-template' => $view,
				),
			),
        );   

		/* the empty route will points to the welcome route */
        $alternates = array('' => false);
    
        parent::__construct('welcome', $primary, $alternates);
    }
}
