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

use SplFileInfo,
	Appfuel\Framework\Exception;

/**
 * 
 */
class PathFinder implements PathFinderInterface
{
	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = null;	

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
     * When includeBase is true prepend the application base path to 
     * the given path and make that the full path
     * 
     * @param   string  $path
     * @param   bool    $includeBase
     * @return  File
     */
    public function __construct($enableBasePath = true)
    {
		if (! defined('AF_BASE_PATH')) {
			throw new Exception("base path constant must be set AF_BASE_PATH");
		}
		$this->basePath = AF_BASE_PATH;

		if (false === $enableBasePath) {
			$this->disableBasePath();
		}
    }

	/**
	 * @return string
	 */
	public function getBasePath()
	{
		return $this->basePath;
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
	 * @throws	Appfuel\Framework\Exception
	 * @return	PathFinder
	 */
	public function setRelativeRootPath($path)
	{	
		if (! is_string($path)) {
			throw new Exception("relative root path must be a string");
		}
		$basePath = $this->getBasePath();

		$found = strpos($path, $basePath);
		if ($this->isBasePathEnabled() && false !== $found) {
			$err  = "relative root can not contain base path when the ";
			$err .= "base path is enabled";
			throw new Exception($err);
		}
		$this->relativeRoot = $path;
		return $this;
	}

	/**
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
	 * @param	string	$path	relative path 
	 * @return	string
	 */
	public function getPath($path = null)
	{
		if (null === $path) {
			$path = '';
		}

		if (! is_string($path)) {
			throw new Exception("path given must be a string");
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
}
