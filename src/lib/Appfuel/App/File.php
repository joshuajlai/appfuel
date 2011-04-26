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
namespace Appfuel\App;

use SplFileInfo;

/**
 * Add knowledge of the base path so developers only need to specify
 * a path relative to base path when working with files.
 */
class File extends SplFileInfo
{
	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = null;	

    /**
     * When includeBase is true prepend the application base path to 
     * the given path and make that the full path
     * 
     * @param   string  $path
     * @param   bool    $includeBase
     * @return  File
     */
    public function __construct($path, $includeBase = true)
    {
        if (TRUE === $includeBase) {
            $this->basePath = AF_BASE_PATH;
            $path = AF_BASE_PATH . DIRECTORY_SEPARATOR . $path;
        }

        parent::__construct($path);
    }

	/**
	 * @return string
	 */
	public function getBasePath()
	{
		return $this->basePath;
	}
}

