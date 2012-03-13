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

$init = new KernelInitializer($base);

/**
 * @todo its not the initializers job to build a front controller
 */
$front = $init->initialize('main')
              ->buildFront();

/**
 * @todo should not have to manually create the context builder
 */
$useUri  = true;
$builder = new MvcContextBuilder();

$context = $builder->useServerRequestUri()
                   ->defineInputFromDefaults($useUri)
                   ->build();

$context = $front->run($context);

$view    = $context->getView();
$code    = $context->getExitCode();
$headers = $context->get('http-headers', array());
if (! is_array($headers) || empty($headers)) {
    $headers = null;
}

$response = new HttpResponse($view, $code, null, $headers);
$output   = new HttpOutput();
$output->render($response);

exit($code);
