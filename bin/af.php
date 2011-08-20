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

use Appfuel\AppManager;

/* we kmow we are in the bin directory and one level up is the base path */
$base = realpath(dirname(__FILE__) . '/..');
$file = $base     . DIRECTORY_SEPARATOR . 
		'lib'	  . DIRECTORY_SEPARATOR .
		'Appfuel' . DIRECTORY_SEPARATOR .
		'AppManager.php';

if (! file_exists($file)) {
	$err = "Could not locate Manager file at ($file)\n";
	fwrite(STDERR, $err);
	exit;
}
require_once $file;

AppManager::initialize($base, 'config/app.ini');
echo "\n", print_r($_SERVER,1), "\n";exit; 
