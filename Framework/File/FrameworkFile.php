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

use SplFileInfo;

/**
 * Add knowledge of the base path so developers only need to specify
 * a path relative to base path when working with files.
 */
class FrameworkFile extends SplFileInfo implements FrameworkFileInterface
{
	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = null;	

	/**
	 * Full path is used because SplFileInfo::getRealPath returns an 
	 * empty string when the file does not exist, but sometimes we want
	 * that path without having concatenate the results of other methods.
	 * @var string
	 */
	protected $fullPath = null;

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
            $this->fullPath = AF_BASE_PATH . DIRECTORY_SEPARATOR . $path;
        } else {
			$this->basePath = null;
			$this->fullPath = $path;
		}

        parent::__construct($this->fullPath);
    }

	/**
	 * @return string
	 */
	public function getBasePath()
	{
		return $this->basePath;
	}

	/**
	 * @return string
	 */
	public function getFullPath()
	{
		return $this->fullPath;
	}
}

