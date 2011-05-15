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
namespace Appfuel\App\View;

use Appfuel\App\View\File as ClientsideFile;

/**
 *
 */
class File extends ClientsideFile
{

    /**
	 * Hard codes this file to the clientside directory inside the base path.
	 * the namespace allows you to choose which namespace to use inside the
	 * clientside directory and an empty string will ignore the namespace 
	 * entirely
	 *
     * @param   string  $path		relative path from clientside dir
	 * @param	string	$namespace	subdirectory in clientside
     * @return  File
     */
    public function __construct($path)
    {	
		$path = 'html' . DIRECTORY_SEPARATOR . $path;
        parent::__construct($path);
    }
}
