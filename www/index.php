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
use Appfuel\App\AppHandler;

$base = realpath(dirname(__FILE__) . '/../');
$file = "{$base}/lib/Appfuel/App/AppHandler.php";
if (! file_exists($file)) {
    throw new LogicException("Could not find app runner at -($file)");
}
require_once $file;

$handler = new AppHandler($base);
$handler->loadConfigFile('app/config/config.php', 'main')
        ->initializeFramework();

$uri     = $handler->createUriFromServerSuperGlobal();
$key     = $uri->getRouteKey();
$format  = $uri->getRouteFormat();
$route   = $handler->findRoute($uri);
$input   = $handler->createRestInputFromBrowser($uri);
$context = $handler->createContext($key, $input);
$handler->initializeApp($route, $context)
		->setupView($route, $context, $format)
		->runAction($context)
		->outputHttpContext($route, $context);

exit($context->getExitCode());
