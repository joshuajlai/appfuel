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
namespace Appfuel\App\View\Html;

use Appfuel\App\View\ClientsideFile;

/**
 * Extend the relative path to start from clientside/appfuel/html instead of
 * clientside/appfuel
 */
class File extends ClientsideFile
{

    /**
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
