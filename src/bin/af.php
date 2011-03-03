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

$base = realpath(dirname(__FILE__) . '/..');

require_once $base . '/Appfuel/Dependency.php';

$dep = new \Appfuel\Dependency($base);
echo "\n", print_r($dep->requireFiles($dep->getFiles()), 1), "\n";exit;

