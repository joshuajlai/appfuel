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
class RouteDeniedException extends RunTimeException
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

		$msg = "access denied: insufficient permissions for this route ";
		$msg = "-($route) uri: -($uri)";
		parent::__construct($msg, 403);
	}
}
