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

/* we kmow we are in the bin directory and one level up is the base path */
$base = realpath(dirname(__FILE__) . '/..');


$file = $base     . DIRECTORY_SEPARATOR . 
		'Appfuel' . DIRECTORY_SEPARATOR .
		'Manager.php';

if (! file_exists($file)) {
	$err = "Could not locate App Builder file at ($file)\n";
	fwrite(STDERR, $err);
	exit;
}

\Appfuel\Manager::init();

$msg = \Appfuel\Manager::createMessage();
\Appfuel\Manager::run($msg);
