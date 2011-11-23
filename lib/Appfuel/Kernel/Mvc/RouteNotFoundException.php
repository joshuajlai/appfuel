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
namespace Appfuel\Kernel\Mvc;

use RunTimeException;

/**
 * Custom kernel exception to indicate that a route has not been mapped
 */
class RouteNotFoundException extends RunTimeException
{
    /**
     * @param   string  $requestString
     * @return  Uri
     */
    public function __construct($route, $uri)
	{
		if (! is_string($route)) {
			$route = '<undefined route key>';
		}

		if (! is_string($uri)) {
			$uri = '<undefined uri string>';
		}

		$msg = "The route -($route) has not been mapped. uri: -($uri)";
		parent::__construct($msg, 400);
	}
}
