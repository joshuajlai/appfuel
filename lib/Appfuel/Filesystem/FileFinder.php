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
namespace Appfuel\Filesystem;

use SplFileInfo,
	RunTimeException,
	InvalidArgumentException;

/**
 * Find files and directories without caring about the absolute path. Base path
 * is used by default but can be disabled and the path used in the constructor
 * will act as the root path for the method getPath
 */
class FileFinder implements FileFinderInterface
{
	/**
	 * Absolute path to top level directory of the application. This is found
	 * in a constant set by the framework called AF_BASE_PATH, but can be
	 * disabled.
	 * @var string
	 */
	protected $basePath = null;

	/**
	 * acts as the root path for method getPath when no path bath it is the
	 * root path.
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
			$err = "base path constant must be set AF_BASE_PATH";
			throw new LogicException($err);
		}

		/* set only a string that is not empty since the default relative
		 * root is empty
		 */
		if (is_string($path) && ($path = trim($path))) {
			$this->setRelativeRootPath($path);
		}

		if (true === $isBasePath) {
			$this->setBasePath(AF_BASE_PATH);
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
	 * @return	bool
	 */
	public function isBasePath()
	{
		if (defined('AF_BASE_PATH') && AF_BASE_PATH === $this->basePath) {
			return true;
		}

		return false;
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
			$err = "relative root path must be a string";
			throw new InvalidArgumentException($err);
		}

		$basePath = $this->getBasePath();
		if (! empty($basePath)) {
			$found = strpos($path, $basePath);
			if (false !== $found) {
				$err  = "relative root can not contain base path when the ";
				$err .= "base path is exists";
				throw new RunTimeException($err);
			}
		}

		/* remove the forward slash at the end if it exists */
		$len = strlen($path);
		if ($len > 0 && '/' === $path{$len-1}) {
			$path = substr($path, 0, $len-1);
		}
		$this->relativeRoot = $path;
		return $this;
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

		if (! is_string($path) &&
			! (is_object($path) && is_callable(array($path, '__toString')))) {
			return false;
		}
			
		$path =(string) $path;
		$root = $this->resolveRootPath();
		if (empty($path)) {
			return $root;
		}

		if ('/' === $path{0}) {
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
		if (file_exists($this->getPath($path))) {
			return true;
		}
		
		return false;
	}

	public function isWriteable($path = null)
	{
		if (is_writable($this->getPath($path))) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isReadable($path = null)
	{
		if (is_writable($this->getPath($path))) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isFile($path = null)
	{
		if (is_file($this->getPath($path))) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isDir($path = null)
	{
		if (is_file($this->getPath($path))) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isLink($path = null)
	{
		if (is_link($this->getPath($path))) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @return	bool
	 */
	public function requireFile($path, $isThrow = true, $msg = '')
	{
		$full = $this->getPath($path);
		if (file_exists($full)) {
			return require $full;
		}

		if (false === $isThrow) {
			return false;
		}
		
		$err = "file not found at -($full)";
		if (is_string($msg) && ! empty($msg)) {
			$err = $msg;
		}
		throw new RunTimeException($err);
	}

	/**
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @return	bool
	 */
	public function requireOnceFile($path, $isThrow = true, $msg = '')
	{
		$full = $this->getPath($path);
		if (file_exists($full)) {
			return require_once $full;
		}

		if (false === $isThrow) {
			return false;
		}
		
		$err = "file not found at -($full)";
		if (is_string($msg) && ! empty($msg)) {
			$err = $msg;
		}
		throw new RunTimeException($err);
	}

	/**
	 * @param	string
	 * @return	mixed
	 */
	public function includeFile($path)
	{
		$full = $this->getPath($path);
		if (file_exists($full)) {
			return include $full;
		}
	
		return false;
	}

	/**
	 * @param	string
	 * @return	mixed
	 */
	public function includeOnceFile($path)
	{
		$full = $this->getPath($path);
		if (file_exists($full)) {
			return include_once $full;
		}
	
		return false;
	}

	/**
	 * @param	string	$path
	 * @return	null
	 */
	protected function setBasePath($path)
	{
		if (! is_string($path)) {
			$err = 'base path must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->basePath = $path;
	}

	/**
	 * Checks for the forward slash on the relative root so you don't end
	 * up the a path that has double slashes.
	 * 
	 * @return	string
	 */
	protected function resolveRootPath()
	{
		$root = $this->getRelativeRootPath();
		$base = $this->getBasePath();
		if (! $this->isBasePath()) {
			return $root;
		}

		if (empty($root)) {
			return $base;
		} 
			
		if ('/' === $root{0}) {
			$root = "{$base}{$root}";
		}
		else {
			$root = "{$base}/{$root}";
		}

		return $root;
	}
}
