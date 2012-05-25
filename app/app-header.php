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
use Appfuel\App\AppDetail,
	Appfuel\App\AppHandler,
	Appfuel\Config\ConfigLoader,
	Appfuel\Config\ConfigRegistry;

$sep  = DIRECTORY_SEPARATOR;
$base = realpath(__DIR__ . '/../');
$src  = "$base{$sep}package";
if (! defined('AF_BASE_PATH')) {
	define('AF_BASE_PATH', $base);
}

/*
 * Load dependent framework files into memory before the autoloader.
 * This allows the framework tasks to be run earlier and not have to 
 * depend on the autoloader to be found.
 */ 
$file = "{$base}{$sep}app{$sep}kernel-dependencies.php";
if (! file_exists($file)) {
	$err = "could not find kernel dependency file at -($file)";
	throw new RunTimeException($err);
}
$list = require $file;

/*
 * determine if the calling script has classes to be be manually
 * loaded into memory and add them to the end of the kernel's list
 */
if (isset($dependList) && is_array($dependList)) {
	$list = array_splice($list, count($list), 0, $dependList);
}

foreach ($list as $class => $file) {
	if (class_exists($class) || interface_exists($class, false)) {
		continue;	
	}
	$absolute = "{$src}{$sep}{$file}";
	if (! file_exists($absolute)) {
		$err = "could not find kernel dependency at -($absolute)";
		throw new RunTimeException($err);
	}

	require $absolute;
}
unset($file, $list, $dependList, $class, $asbsolute, $err);

/*
 * load configuration data into the config registry. If the including script
 * sets $configData then don't look for a file, otherwise use the AppStructure
 * which holds the application directory structure and location of config file
 * to be loaded based on a config key
 */
$loader = new ConfigLoader();
$detail = new AppDetail($base);
define('AF_CODE_PATH', $detail->getPackage());

if (isset($configData)) {
	$loader->set($configData);
}
else {
	if (! isset($configKey)) {
		$configKey = 'web';
	}
	$loader->loadFile($detail->getConfigFile($configKey), true);
}

/*
 * list of framework startup tasks to be run after initialization. The 
 * including script can append, prepend or replaces these when needed. 
 */
$tasks = array(
	'Appfuel\Kernel\PHPIniTask',
    'Appfuel\Kernel\PHPErrorTask',
    'Appfuel\Kernel\PHPPathTask',
    'Appfuel\Kernel\PHPDefaultTimezoneTask',
    'Appfuel\Kernel\PHPAutoloaderTask',
    'Appfuel\Kernel\FaultHandlerTask',
    'Appfuel\Kernel\DependencyLoaderTask',
    'Appfuel\Kernel\RouteListTask',
	'Appfuel\Validate\ValidationStartupTask'
);

if (! isset($taskAction) || ! is_string($taskAction) || empty($taskAction)) {
	$taskAction = 'append';
}

if (isset($fwTasks) && is_array($fwTasks)) {
	switch($taskAction) {
		case 'append': 
			array_splice($tasks, count($tasks), 0, $fwTasks);
			break;
		case 'prepend':
			array_splice($tasks, 0, 0, $fwTasks);
			break;
		case 'replace':
			$tasks = $fwTasks;
			break;			
	}
}

$handler = new AppHandler($detail);
$handler->initialize($tasks);

unset($tasks, $detail, $loader);
