<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
use Appfuel\App\Manager;

/*
 * Application root directory  
 */
$base = realpath(dirname(__FILE__) . '/..');

/*
 * Application factory used to create factory class
 */
$file = "$base/lib/Appfuel/App/Manager.php";
if (! file_exists($file)) {
    throw new Exception(
        "Initialization script error: App Manager File not found"
    );
}
require_once $file;
unset($file);

Manager::run($base, "$base/config/app.ini", 'Web');


