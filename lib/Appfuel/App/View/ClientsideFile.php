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

use Appfuel\Framework\Exception,
	Appfuel\App\File;

/**
 * File object that always starts at the clientside directory. It is used by
 * view templates to bind scope to template files. The class always the 
 * developer to focus on the relative path to the template file instead of
 * how to get the base path.
 */
class ClientsideFile extends File
{

	/**
	 * Relative path to the file from the clientside directory
	 * @var string
	 */
	protected $clientsidePath = null;

	/**
	 * The top level directory for all clientside files
	 * @var string
	 */
	protected $rootDirName = 'clientside';

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
    public function __construct($path, $namespace = null)
    {
		if (! $this->isValidString($path)) {
			throw new Exception("Invalid path: must be a non empty string");
		}

		if (empty($namespace)) {
			$namespace = $this->discoverNamespace();
		}
		$this->setNamespace($namespace);
		$path = $this->buildClientsidePath($path);
		$this->setClientsidePath($path);

		$includeBasePath = true;
        parent::__construct($path, $includeBasePath);
    }

	/**
	 * @param	string	$path	relative path to clientside file
	 * @return	string
	 */
	public function buildClientsidePath($path)
	{
		$sep       = DIRECTORY_SEPARATOR;
		$rootDir   = $this->getRootDirName();
		$namespace = $this->getNamespace();
		return "{$rootDir}{$sep}{$namespace}{$sep}{$path}";
	}

	public function setClientsidePath($path)
	{
		if (! $this->isValidString($path)) {
			throw new Exception("Invalid path: must be a non empty string");
		}

		$this->clientsidePath = $path;
		return $this;
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

	public function discoverNamespace()
	{
		$class = get_class($this);
		$pos   = strpos($class, '\\');
		if (false === $pos) {
			return false;
		}
		
		return strtolower(substr($class, 0, $pos));
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
