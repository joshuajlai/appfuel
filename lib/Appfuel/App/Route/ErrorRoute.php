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
namespace Appfuel\App\Route;

use Appfuel\Framework\Exception,
	Appfuel\Framework\App\Route\RouteInterface;

/**
 * An error route that allows you to change the response type
 */
class ErrorRoute extends ActionRoute
{
    /**
	 * @param	string	$responseType
     * @return	Route
     */
    public function __construct($responseType = 'html')
    {
		$route     = 'error/handler/invalid';
		$access    = 'public';
		$namespace = 'Appfuel\App\Action\Error\Handler\Invalid';
		parent::__construct($route, $namespace, $access, $responseType);
    }
}
