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
 * File object that always starts at the resources directory
 */
class File extends AppFile
{

	/**
	 * Relative path to the file from the resource directory
	 * @var string
	 */
	protected $resourcePath = null;

    /**
     * @param   string  $path
     * @return  File
     */
    public function __construct($path)
    {
		$this->resourcePath = 'resource' . DIRECTORY_SEPARATOR . $path;
		$includeBasePath = true;
        parent::__construct($path, $includeBasePath);
    }

	/**
	 * @return string
	 */
	public function getResourcePath()
	{
		return $this->resourcePath;
	}

}

