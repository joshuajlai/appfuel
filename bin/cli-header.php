<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2011 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
$base = realpath(dirname(__FILE__) . '/../');
$file = "{$base}/lib/Appfuel/Kernel/AppHandler.php";
if (! file_exists($file)) {
    $err = "Could not find kernel initializer file at $file";
	fwrite(STDERR, $err);
	exit(1);
}
require $file;

$handler = new \Appfuel\Kernel\AppHandler($base);
$handler->loadConfigFile('app/config/config.php', 'main')
        ->initializeFramework();

/* when -v is found capture it then remove it from argv 
 * so env will always be the first argumment regardless
 * of where the option is set
 */
$isVerbose = false;
$index     = array_search('-v', $argv, true);
if (false !== $index) {
    $isVerbose = true;
    unset($argv[$index]);
    $argv = array_values($argv);
	unset($index);
}
