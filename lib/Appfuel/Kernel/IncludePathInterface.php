<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel;

/**
 * Changes the php include path
 */
interface IncludePathInterface
{
    /**
	 * Convert the array of paths into a string with path separators. The i
	 * action parameter is used to determine how to deal with the original 
	 * include path. should we append, prepend, or replace it
     * 
     * @param   mixed   $paths
     * @param   string  $action     how to deal with the original path
     * @return  NULL    
     */
	public function setPath($paths, $action = 'replace');
}
