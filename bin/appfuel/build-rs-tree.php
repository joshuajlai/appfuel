#!/usr/bin/env php
<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2011 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
use Appfuel\Kernel\KernelInitializer,
    Appfuel\Html\Resource\ResourceTreeWriter,
    Appfuel\Html\Resource\ResourceTreeBuilder;

$base = realpath(dirname(__FILE__) . '/../../');
$file = "{$base}/lib/Appfuel/Kernel/KernelInitializer.php";
if (! file_exists($file)) {
    throw new \Exception("Could not find kernel initializer file at $file");
}
require_once $file;

$config = array('main' => array(
    'base-path'             => $base,
    'enable-autoloader'     => true,
    'default-timezone'      => 'America/Los_Angeles',
    'display-errors'        => 'on',
    'error-reporting'       => 'all, strict',

));
$init  = new KernelInitializer($base);
$init->initialize('main', $config);

try {
    $builder = new ResourceTreeBuilder();
    $tree = $builder->buildTree();

    $writer = new ResourceTreeWriter();
    $writer->writeTree($tree);
} catch (Exception $e) {
    fwrite(STDERR, 'could not write resource tree: '. $e->getMessage() . "\n");
    exit;
}

echo "tree built\n";
exit(0);
