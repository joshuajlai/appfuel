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
namespace Appfuel\View;

use Appfuel\Framework\Exception,
	Appfuel\Framework\File\FrameworkFile;

/**
 * The view file hides the absolute path to a directory of templates and
 * allows the user to specify a relative path from that directory. 
 */
class ViewFile extends FrameworkFile
{

	/**
	 * Relative path to the file from the namespace directory which is in the
	 * root directory. Ex) clientside/appfuel/<relative/path/here>
	 * @var string
	 */
	protected $viewPath = null;

	/**
	 * The top level directory for all clientside files is ui (user interface)
	 * @var string
	 */
	protected $rootDirName = 'ui';

	/**
	 * The child directory under the root. Every application uses there 
	 * namespace to separate their clientside files from other vendors
	 * @var string
	 */
	protected $namespace = 'appfuel';

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
		if (! $this->isValidString($path)) {
			throw new Exception("Invalid path: must be a non empty string");
		}

		$this->setNamespace($this->discoverNamespace());
		$path = $this->buildViewPath($path);
		$this->setViewPath($path);

		$includeBasePath = true;
        parent::__construct($path, $includeBasePath);
    }

	/**
	 * @return string
	 */
	public function getViewPath($absolute = false)
	{
		$path = $this->viewPath;
		if (true === $absolute) {
			$path = "{$this->getBasePath()}/{$path}";
		}

		return $path;
	}

	/**
	 * @return string
	 */
	public function getRootDirName()
	{
		return $this->rootDirName;
	}

	/**
	 * @param	string	$name
	 * @return	ClientsideFile
	 */
	public function setRootDirName($name)
	{
		if (! $this->isValidString($name)) {
			throw new Exception("Invalid name: must be a non empty string");
		}

		$this->rootDirName = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @param	string $dirName
	 * @return	ClientsideFile
	 */
	public function setNamespace($dirName)
	{
		if (! $this->isValidString($dirName)) {
			throw new Exception("Invalid dir name: must be a non empty string");
		}

		$this->namespace = $dirName;
		return $this;
	}

	/**
	 * @param	string	$path
	 * @return	string
	 */
	protected function setViewPath($path)
	{
		if (! $this->isValidString($path)) {
			throw new Exception("Invalid path: must be a non empty string");
		}

		$this->viewPath = $path;
		return $this;
	}


	/**
	 * @param	string	$path	relative path to clientside file
	 * @return	string
	 */
	protected function buildViewPath($path)
	{
		$rootDir   = $this->getRootDirName();
		$namespace = $this->getNamespace();
		return "{$this->getRootDirName()}/{$this->getNamespace()}/{$path}";
	}

	/**
	 * Find the root namespace of the class extending this class or this class
	 * in which case would be appfuel
	 *
	 * @return string
	 */
	protected function discoverNamespace()
	{
		$class = get_class($this);
		$pos   = strpos($class, '\\');
		if (false === $pos) {
			return false;
		}
		
		return strtolower(substr($class, 0, $pos));
	}

	/**
	 * @return bool
	 */
	protected function isValidString($str)
	{
		if (! is_string($str) || empty($str)) {
			return false;
		}

		return true;
	}
}
