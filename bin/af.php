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

$base = realpath(dirname(__FILE__));
$init = require $base . '/af-cli-init.php';
echo "\n", print_r($init,1), "\n";exit;

AppManager::initialize($base, 'config/app.ini');
echo "\n", print_r($_SERVER,1), "\n";exit; 
