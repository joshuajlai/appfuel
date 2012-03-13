#!/usr/bin/env php
<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2011 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
use Appfuel\Kernel\ConfigBuilder,
	Appfuel\Kernel\KernelInitializer,
	Appfuel\Kernel\KernelRegistry;

$base = realpath(dirname(__FILE__) . '/../');
$file = "{$base}/lib/Appfuel/Kernel/KernelInitializer.php";
if (! file_exists($file)) {
    throw new \Exception("Could not find kernel initializer file at $file");
}
require_once $file;

$config = array('main' => array(
	'base-path'				=> $base,
	'enable-autoloader'     => true,
    'default-timezone'      => 'America/Los_Angeles',
    'display-errors'        => 'on',
    'error-reporting'       => 'all, strict',
	
));
$init  = new KernelInitializer($base);
$init->initialize('main', $config);

$args = $_SERVER['argv'];
if (count($args) < 2 || ! isset($args[1])) {
	fwrite(STDERR, "must the env name -(eg. local|dev|qa|production)\n");
	exit;
}
$env = $args[1];
if (! is_string($env) || empty($env)) {
	fwrite(STDERR, "env must be a non empty string");
	exit;
}

$builder = new ConfigBuilder($env);

try {
	$builder->generateConfigFile();
} catch (Exception $e) {
	fwrite(STDERR, 'config build failure: '. $e->getMessage());
	exit;
}

$path = "$base/app/config/config.php";
fwrite(STDOUT, "generated config file: \n$path\n");
exit(0);
