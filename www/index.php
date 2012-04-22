<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
use Appfuel\AppHandler;

$base = realpath(dirname(__FILE__) . '/../');
$file = "{$base}/lib/Appfuel/App/AppHandler.php";
if (! file_exists($file)) {
    throw new LogicException("Could not find app runner at -($file)");
}
require_once $file;

$handler = new AppHandler($base);
$handler->loadConfigFile('app/config/config.php', 'main')
        ->initializeFramework();

$uri      = $handler->createUriFromServerSuperGlobal();
$key      = $uri->getRouteKey(); 
$route   = $handler->findRoute($key);
echo "<pre>", print_r($route->getFormat(), 1), "</pre>";exit;
$input   = $handler->loadRestfulInput();
$context = $handler->createContext($route->getKey(), $input);

$handler->startupApp($route, $context)
       ->setupView($route, $context)
       ->processAction($context);
        
$view = $handler->composeView($context, $route);
$headers = $context->get('http-headers', array());
$handler->httpOutput($view, $headers);
exit($runner->getExitCode());
