#!/usr/bin/env php
<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2011 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
use Appfuel\App\AppHandler;

$base = realpath(dirname(__FILE__) . '/../../');
$file = "{$base}/lib/Appfuel/App/AppHandler.php";
if (! file_exists($file)) {
    $err = "Could not find kernel initializer file at $file";
    throw new LogicException($err);
}
require $file;

$handler = new AppHandler($base);
$handler->loadConfigFile('app/config/config.php', 'main')
        ->initializeFramework();

if (count($argv) < 2) {
	$err = "fuelcell cli must have a route as its first argument \n";
	fwrite(STDERR, $err);
	exit(1);
}
/*
 * default format for cli is text
 */
$uriStr  = $argv[1];
$uri     = $handler->createUri($uriStr);
$key     = $uri->getRouteKey();
$format  = $uri->getRouteFormat();
if (empty($format)) {
	$format = 'text';
}
$route   = $handler->findRoute($uri);
$input   = $handler->createInputFromSuperGlobals($uri);
$context = $handler->createContext($key, $input);
$handler->initializeApp($route, $context)
        ->setupView($route, $context, $format)        
		->runAction($context)
        ->outputConsoleContext($route, $context);

$code = $context->getExitCode();
if ($code >= 200 || $code < 300) {
	$code = 0;
}
exit($context->getExitCode());
