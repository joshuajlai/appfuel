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
use Appfuel\Kernel\PathFinder,
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

$finder = new PathFinder('app/config');
echo "\n", print_r($finder->getPath('master/common.php'),1), "\n";exit;

