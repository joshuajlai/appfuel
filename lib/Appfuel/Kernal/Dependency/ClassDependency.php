<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernal\Dependency;

use Appfuel\Framework\Exception;

/**
 * Used to declare a group of files or namespaces that should be loaded by a 
 * dependency loader which does not use the autoloader.
 */
class ClassDependency implements ClassDependencyInterface
{
	/**
	 * This path will be attatched to each namespace when resolving it into
	 * an absolute path.
	 * @var string
	 */
	protected $rootPath = null;

	/**
	 * List of namespaces to be mapped to file paths
	 * @var array
	 */
	protected $namespaces = array();

	/**
	 * List of files to include. This is needed when the file has more than
	 * one namespace or does not follow namespacing rules
	 * @var array
	 */
	protected $files = array();

	/**
	 * @param	string	$rootPath
	 * @return	ClassDependency
	 */
	public function __construct($rootPath)
	{
		if (! is_string($rootPath)) {
			throw new Exception("root path must be a string");
		}
		$this->rootPath = trim($rootPath);
	}

	/**
	 * @return	string
	 */
	public function getRootPath()
	{
		return $this->rootPath;
	}

	/**
	 * @param	string	
	 * @return	ClassDependency
	 */
	public function addNamespace($ns)
	{
		if (empty($ns) || ! is_string($ns) || ! ($ns = trim($ns))) {
			return $this;
		}

		if (in_array($ns, $this->namespaces)) {
			return $this;
		}

		$this->namespaces[] = $ns;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	ClassDependency
	 */
	public function loadNamespaces(array $list)
	{
		foreach ($list as $ns) {
			$this->addNamespace($ns);
		}

		return $this;
	}

	/**
	 * @return	array
	 */
	public function getNamespaces()
	{
		return $this->namespaces;
	}

	/**
	 * @return	array
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * @param	string	$file
	 * @return	ClassDependency
	 */
	public function addFile($file)
	{
		if (empty($file) || ! is_string($file) || ! ($file = trim($file))) {
			return $this;
		}

		if (in_array($file, $this->files)) {
			return $this;
		}

		$this->files[] = $file;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	ClassDependency
	 */
	public function loadFiles(array $list)
	{
		foreach ($list as $file) {
			$this->addFile($file);
		}

		return $this;
	}
}
