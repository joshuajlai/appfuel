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
use Appfuel\Http\HttpOutput,
    Appfuel\Http\HttpResponse,
    Appfuel\Kernel\KernelInitializer,
    Appfuel\Kernel\Mvc\MvcContextBuilder;

$base = realpath(dirname(__FILE__) . '/../');
$file = "{$base}/lib/Appfuel/Kernel/KernelInitializer.php";
if (! file_exists($file)) {
    throw new LogicException("Could not find kernel initializer file at $file");
}
require_once $file;

$init    = new KernelInitializer($base);
$factory = $init->initialize('legacy')
                ->createMvcFactory();

if (! $factory instanceof MvcFactoryInterface) {
    $err = "mvc factory must implment Appfuel\Kernel\Mvc\MvcFactoryInterface";
    throw new LogicException($err);
}

$uri   = $factory->createUriFromServerSuperGlobal();
$key   = $uri->getRouteKey();
$route = $factory->createRouteDetail($key);
if (! $route instanceof MvcRouteDetailInterface) {
    $err = "could not resolve route detail for -({$key})";
    throw new LogicException($err);
}
$init->runStartupTasks($route);

$input  = $factory->createInputFromSuperGlobals($uri);
$viewDetail  = $route->getViewDetail();
echo "<pre>", print_r($route, 1), "</pre>";exit;

$view = $factory->createViewBuilder()
                ->buildView();
$context = $factory->createContext($key, $input);

$context = $front->run($context);

$view = $context->getView();
$builder = new ViewBuilder();
$content = $builder->buildView($context->getViewDetail());
$code    = $context->getExitCode();
$headers = $context->get('http-headers', array());
if (! is_array($headers) || empty($headers)) {
    $headers = null;
}

$response = new HttpResponse($content, $code, null, $headers);
$output   = new HttpOutput();
$output->render($response);

exit($code);


