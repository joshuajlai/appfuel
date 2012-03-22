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

use RunTimeException,
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
	protected $root = '';

    /**
     *  
     * @param   string  $path
     * @param   bool    $isBasePath
     * @return  File
     */
    public function __construct($path = null, $isBasePath = true)
    {
		if (true === $isBasePath) {
			if (! defined('AF_BASE_PATH')) {
				$err = "base path constant must be set AF_BASE_PATH";
				throw new LogicException($err);
			}
			$this->setBasePath(AF_BASE_PATH);
		}

		/* set only a string that is not empty since the default relative
		 * root is empty
		 */
		if (is_string($path)) {
			$this->setRootPath($path);
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
	public function getRootPath()
	{
		return $this->root;
	}

	/**
	 * @throws  RunTimeException
	 * @throws	InvalidArgumentException
	 * @return	PathFinder
	 */
	public function setRootPath($path)
	{	
		if (! is_string($path) && 
			! (is_object($path) && is_callable(array($path, '__toString')))) {
			$err  = "the root path must be a string or an object that ";
			$err .= "implements __toString";
			throw new InvalidArgumentException($err);
		}
		$path =(string) $path;

		if ($this->isBasePath()) {
			$basePath = $this->getBasePath();
			$found = strpos($path, $basePath);
			if (false !== $found) {
				$err  = "root path can not contain base when it already exists";
				throw new RunTimeException($err);
			}
		}

		$this->root = $path;
		return $this;
	}

	/**
	 * Checks for the forward slash on the relative root so you don't end
	 * up the a path that has double slashes.
	 * 
	 * @return	string
	 */
	public function getResolvedRootPath()
	{
		$root = $this->getRootPath();
		/* 
		 * if there is no base path we must preserve the absoluste path
		 */
		if (! $this->isBasePath()) {
			return ('/' === $root) ? $root : rtrim($root, " \n\t/");
		}

		$base = rtrim($this->getBasePath(), " \n\t/");
		return rtrim("$base/$root", " \n\t/");
	}

	/**
	 * Creates an absolute path by resolving base path (when it exists) root
	 * path and the path passed in as a parameter
	 * 
	 * @param	string	$path	relative path 
	 * @return	string | false when param is invalid
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
		$root = $this->getResolvedRootPath();
		if (empty($path)) {
			return $root;
		}
		else if (empty($root)) {
			return $path;
		}

		$path = ltrim($path, " \n\t/");
		if ('/' === $root) {
			return "/{$path}";
		}

		return "$root/$path";
	}

	public function fileExists($fullPath)
	{
		if (file_exists($fullPath)) {
			return true;
		}

		false;
	}

	/**
	 * @param	string $path
	 * @return	bool
	 */
	public function pathExists($path = null)
	{
		if ($this->fileExists($this->getPath($path))) {
			return true;
		}
		
		return false;
	}

	/**
	 * @param	string $path
	 * @return	bool
	 */
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
		if (is_readable($this->getPath($path))) {
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
		if (is_dir($this->getPath($path))) {
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
	public function requireFile($path, $isThrow = true, $msg = '', $code = 404)
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
		throw new RunTimeException($err, $code);
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
}
