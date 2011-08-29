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
namespace Appfuel\Framework\File;

/**
 * File object the allows the developer to only require the relative path
 * to the file or directory they want starting from the top level dir.
 */
class AppfuelFile extends FrameworkFile
{
    /**
     * @param   string  $path
	 * @param	string	$top	top level directory from the basepath
     * @return  AppfuelFile
     */
    public function __construct($path, $top = 'lib')
    {
		$valid = array('lib', 'config', 'db', 'test', 'ui', 'www', 'codegen');
		if (! in_array($top, $valid)) {
			throw new Exception("invalid top level directory given as ($top)");
		}

        parent::__construct("$top/$path", true);
    }
}

