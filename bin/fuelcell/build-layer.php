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
use Appfuel\Kernel\KernelInitializer,
    Appfuel\Kernel\Mvc\MvcContextBuilder,
    Appfuel\Kernel\Mvc\MvcFactoryInterface,
    Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

$base = realpath(dirname(__FILE__) . '/../../');
$file = "{$base}/lib/Appfuel/Kernel/KernelInitializer.php";
if (! file_exists($file)) {
    throw new \Exception("Could not find kernel initializer file at $file");
}
require_once $file;

$config = array('main' => array(
    'base-path'             => $base,
    'enable-autoloader'     => true,
    'default-timezone'      => 'America/Los_Angeles',
    'display-errors'        => 'on',
    'error-reporting'       => 'all, strict',

));
$init  = new KernelInitializer($base);
$factory = $init->initialize('main', $config)
				->createMvcFactory();

if (! $factory instanceof MvcFactoryInterface) {
    $err = "mvc factory must implment Appfuel\Kernel\Mvc\MvcFactoryInterface";
    throw new LogicException($err);
}

if (count($argv) < 2) {
	$err = "fuelcell cli must have a route as its first argument";
	fwrite(STDERR, $err);
	exit(1);
}

$routeStr = $argv[1];
$uri      = $factory->createUri($routeStr);
$key      = $uri->getRouteKey();
$parts  = explode('.', $key);
$key    = current($parts);
$format = strtolower(next($parts));
if (empty($format)) {
    $format = 'text';
}

$input  = $factory->createInputFromSuperGlobals($uri);
$route  = $factory->createRouteDetail($key);
if (! $route instanceof MvcRouteDetailInterface) {
    $err = "could not resolve route detail for -({$key})";
    throw new LogicException($err);
}

$context = $factory->createContext($key, $input);
$init->runStartupTasks($route);

$viewBuilder = $factory->createViewBuilder();
$viewBuilder->setupView($context, $route, $format);

$front   = $factory->createFront();
$context = $front->run($context);
$content = $viewBuilder->composeView($context, $route, $format);

fwrite(STDOUT, $content);
exit($context->getExitCode());

ResourceTreeManager::loadTree();

$name    = new PkgName("yui3:layer.fw-global");
$stack   = new FileStack();
$content = new ContentStack();
$layer   = ResourceTreeManager::loadLayer($name, $stack);

$buildDir     = $layer->getBuildDir();
$buildFile    = $layer->getBuildFile();
$jsBuildFile  = "$buildFile.js";
$cssBuildFile = "$buildFile..css";

$finder = new FileFinder('resource');
$reader = new FileReader($finder);

$jsList = $stack->get('js');
foreach ($jsList as $file) {
	$text = $reader->getContent($file);
	if (false === $text) {
		$err = "could not read contents of file -($file)";
		throw new RunTimeException($err);
	}
	$content->add($text);	
}

$result = '';
foreach ($content as $data) {
	$result .= $data . PHP_EOL;
}

$writer = new FileWriter($finder);
if (! $finder->isDir($buildDir)) {
	if (! $writer->mkdir($buildDir, 0755, true)) {
		$err = "could not create dir at -({$finder->getPath($buildDir)})";
		throw new RunTimeException($err);
	}
}
$ok = $writer->putContent($result, $jsBuildFile);
echo "\n", print_r($ok,1), "\n";exit;
exit(0);
