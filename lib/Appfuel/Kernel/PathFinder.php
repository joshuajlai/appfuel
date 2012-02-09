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
namespace Appfuel\Kernel;

use SplFileInfo,
	RunTimeException,
	InvalidArgumentException;

/**
 * The path finder is used to encapsulate the knowledge of the base path and
 * whatever relative path the developer specifies in the constructor. This 
 * allows the code to resolve absolute paths without ever caring about what
 * they are or how to build them. They can care only about the relative path
 */
class PathFinder implements PathFinderInterface
{
	/**
	 * Flag used to determine if the application base path should be include
	 * @var bool
	 */
	protected $isBaseEnabled = true;

	/**
	 * The relative root is the directory path from base path to what 
	 * the findPath method would accept as the relative path
	 * @var string
	 */
	protected $relativeRoot = '';

    /**
     *  
     * @param   string  $path
     * @param   bool    $isBasePath
     * @return  File
     */
    public function __construct($path = '', $isBasePath = true)
    {
		if (! defined('AF_BASE_PATH') && true === $isBasePath) {
			throw new RunTimeException(
				"base path constant must be set AF_BASE_PATH"
			);
		}

		/* set only a string that is not empty since the default relative
		 * root is empty
		 */
		if (is_string($path) && ($path = trim($path))) {
			$this->setRelativeRootPath($path);
		}

		if (false === $isBasePath) {
			$this->disableBasePath();
		}
    }

	/**
	 * @return string
	 */
	public function getBasePath()
	{
		return AF_BASE_PATH;
	}

	/**
	 * @return	PathFinder
	 */
	public function enableBasePath()
	{
		$this->isBaseEnabled = true;
		return $this;
	}

	/**
	 * @return	PathFinder
	 */
	public function disableBasePath()
	{
		$this->isBaseEnabled = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isBasePathEnabled()
	{
		return $this->isBaseEnabled;
	}

	/**
	 * @return	string
	 */
	public function getRelativeRootPath()
	{
		return $this->relativeRoot;
	}

	/**
	 * @throws  RunTimeException
	 * @throws	InvalidArgumentException
	 * @return	PathFinder
	 */
	public function setRelativeRootPath($path)
	{	
		if (! is_string($path)) {
			throw new InvalidArgumentException(
				"relative root path must be a string"
			);
		}
		$basePath = $this->getBasePath();

		$found = strpos($path, $basePath);
		if ($this->isBasePathEnabled() && false !== $found) {
			$err  = "relative root can not contain base path when the ";
			$err .= "base path is enabled";
			throw new RunTimeException($err);
		}
		$this->relativeRoot = $path;
		return $this;
	}

	/**
	 * Checks for the forward slash on the relative root so you don't end
	 * up the a path that has double slashes.
	 * @return	string
	 */
	public function resolveRootPath()
	{
		$root = $this->getRelativeRootPath();
		if ($this->isBasePathEnabled()) {
			$base = $this->getBasePath();
			if (empty($root)) {
				$root = $base;
			} 
			else if ('/' === $root{0}) {
				$root = "{$base}{$root}";
			}
			else {
				$root = "{$base}/{$root}";
			}
		}

		return $root;
	}

	/**
	 * Returns an absolute path to with the specified path appended to it
	 * 
	 * @param	string	$path	relative path 
	 * @return	string
	 */
	public function getPath($path = null)
	{
		if (null === $path) {
			$path = '';
		}

		if (! is_string($path)) {
			throw new InvalidArgumentException("path given must be a string");
		}

		$root = $this->resolveRootPath();
		if (empty($path)) {
			$result = $root;
		}
		else if ('/' === $path{0}) {
			$result = "{$root}{$path}";
		}
		else {
			$result = "{$root}/{$path}";
		}

		return $result;
	}

	/**
	 * @param	string $path
	 * @return	bool
	 */
	public function fileExists($path = null)
	{
		$full = $this->getPath($path);
		if (file_exists($full)) {
			return true;
		}
		
		return false;
	}

	public function requireFile($path, $throwException = true, $msg = '')
	{
		$full = $this->getPath($path);
		if (file_exists($full)) {
			return require $full;
		}

		if (false === $throwException) {
			return false;
		}

		throw new RunTimeException("file not found at -($full)");
	}
}
