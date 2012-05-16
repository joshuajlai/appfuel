<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
use Appfuel\App\AppHandlerInterface;

$header = realpath(__DIR__ . '/../app/app-header.php');
if (! file_exists($header)) {
    $err = "could not find the app header script";
    throw new RunTimeException($err);
}
$configKey = 'test';
require $header;
if (! isset($handler) || ! $handler instanceof AppHandlerInterface) {
    $err  = "app handler was not created or does not implement Appfuel\Kernel";    $err .= "\AppHandlerInterface";    throw new LogicException($err);
}

