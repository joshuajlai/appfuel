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
namespace Appfuel\Framework\App;

use Appfuel\Framework\App\Action\ControllerInterface,
    Appfuel\Framework\App\Route\RouteInterface,
    Appfuel\Framework\App\ContextInterface,
    Appfuel\App\Action\Error\Handler\Invalid\Controller as ErrorController;


/**
 * Handle dispatching the request and outputting the response
 */
interface FrontControllerInterface
{
}
