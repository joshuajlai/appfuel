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

use Appfuel\App\File as AppFile;

/**
 * File object that always starts at the clientside directory. It is used by
 * view templates to bind scope to template files. The class always the 
 * developer to focus on the relative path to the template file instead of
 * how to get the base path.
 */
class File extends AppFile
{

	/**
	 * Relative path to the file from the clientside directory
	 * @var string
	 */
	protected $clientsidePath = null;

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
    public function __construct($path, $namespace = 'appfuel')
    {
		if (! is_string($path) || empty($path)) {
			throw new Exception("Invalid path: must be a non empty string");
		}

		$filePath = 'clientside' . DIRECTORY_SEPARATOR;
		if (is_string($namespace) && ! empty($namespace)) {
			$filePath .= $namespace . DIRECTORY_SEPARATOR;
		}

		
		$this->clientsidePath = "{$filePath}{$path}";

		$includeBasePath = true;
        parent::__construct($this->clientsidePath, $includeBasePath);
    }

	/**
	 * @return string
	 */
	public function getClientsidePath($absolute = false)
	{
		$path = $this->clientsidePath;
		if (true === $absolute) {
			$path = $this->getBasePath() . DIRECTORY_SEPARATOR . $path;
		}

		return $path;
	}
}
