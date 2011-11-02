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
use Appfuel\App\Dependency;

/* we kmow we are in the bin directory and one level up is the base path */
$base = realpath(dirname(__FILE__) . '/..');

$file = "$base/lib/Appfuel/App/Dependency.php";
if (! file_exists($file)) {
    $err = "Could not locate Initializer file at ($file)\n";
    fwrite(STDERR, $err);
    exit;
}
require_once $file;

$depend = new Dependency("$base/lib");
$depend->load();

echo "\n", print_r($depend,1), "\n";exit;

