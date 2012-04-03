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
use Appfuel\Http\HttpOutput,
    Appfuel\Http\HttpResponse,
	Appfuel\View\ViewData,
	Appfuel\Html\HtmlPage,
	Appfuel\Html\HtmlPageConfiguration,
    Appfuel\Kernel\KernelInitializer,
    Appfuel\Kernel\Mvc\MvcContextBuilder,
	Appfuel\Kernel\Mvc\MvcFactoryInterface,
	Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

$base = realpath(dirname(__FILE__) . '/../');
$file = "{$base}/lib/Appfuel/Kernel/KernelInitializer.php";
if (! file_exists($file)) {
    throw new LogicException("Could not find kernel initializer file at $file");
}
require_once $file;

$init    = new KernelInitializer($base);
$factory = $init->initialize('main')
                ->createMvcFactory();

if (! $factory instanceof MvcFactoryInterface) {
    $err = "mvc factory must implment Appfuel\Kernel\Mvc\MvcFactoryInterface";
    throw new LogicException($err);
}

/*
 * parse view format out of the route key: takes the form route-key.format
 * ex) my-route-key.json 
 */
$uri    = $factory->createUriFromServerSuperGlobal();
$key    = $uri->getRouteKey();
$parts  = explode('.', $key);
$key    = current($parts);
$format = strtolower(next($parts));
if (empty($format)) {
	$format = 'html';
}

$input = $factory->createInputFromSuperGlobals($uri);
$route = $factory->createRouteDetail($key);
if (! $route instanceof MvcRouteDetailInterface) {
    $err = "could not resolve route detail for -({$key})";
    throw new LogicException($err);
}

$init->runStartupTasks($route);
$context	 = $factory->createContext($key, $input);
$viewBuilder = $factory->createViewBuilder();
$viewBuilder->setupView($context, $route, $format);

$front   = $factory->createFront();
$context = $front->run($context);

$content = $viewBuilder->composeView($context, $route);
echo "<pre>", print_r($content, 1), "</pre>";exit;	

$code    = $context->getExitCode();
$headers = $context->get('http-headers', array());
if (! is_array($headers) || empty($headers)) {
    $headers = null;
}

$response = new HttpResponse($content, $code, null, $headers);
$output   = new HttpOutput();
$output->render($response);

exit($code);


