<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
use Appfuel\App\AppHandlerInterface;

$header = realpath(dirname(__FILE__) . '/../app/app-header.php');
if (! file_exists($header)) {
	$err = "could not find the app header script";
	throw new RunTimeException($err);
}
$configKey = 'web';
require $header;
if (! isset($handler) || ! $handler instanceof AppHandlerInterface) {
    $err  = "app handler was not created or does not implement Appfuel\Kernel";
    $err .= "\AppHandlerInterface";
    throw new LogicException($err);
}

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
