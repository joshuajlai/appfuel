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
namespace Appfuel\Kernal;

/**
 * Changes the php include path
 */
class IncludePath implements IncludePathInterface
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
	public function setPath($paths, $action = 'replace')
	{
        /* a single path was passed in */
        if (is_string($paths) && ! empty($paths)) {
            $pathString = $paths;
        } else if (is_array($paths) && ! empty($paths)) {
            $pathString = implode(PATH_SEPARATOR, $paths);
        } else {
            return FALSE;
        }

        /*
         * The default action is to replace the include path. If
         * action is given with either append or prepend the 
         * paths will be concatenated accordingly
         */
        $includePath = get_include_path();
        if ('append' === $action) {
            $pathString = $includePath . PATH_SEPARATOR . $pathString;
        } else if ('prepend' === $action) {
            $pathString .= PATH_SEPARATOR . $includePath;
        }

        return set_include_path($pathString);
	}
}
