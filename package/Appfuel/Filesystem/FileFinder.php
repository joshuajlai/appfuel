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

use DomainException,
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
	 * in a constant set by the framework called AF_BASE_PATH
	 * @var string
	 */
	protected $basePath = null;

	/**
	 * This is the relative point of concatenation used by getPath.
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
				$err  = 'When the 2nd param $isBasePath is true then the ';
				$err .= 'constant AF_BASE_PATH must be set ';
				throw new DomainException($err);
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
	 * @return	FileFinder
	 */
	public function setRootPath($path)
	{
		$isRelative = $this->isBasePath();
		$this->root = $this->validatePath($path, $isRelative);
		return $this;
	}

	/**
	 * Creates an absolute path by resolving base path (when it exists) root
	 * path and the path passed in as a parameter
	 * 
	 * @param	string	$path	relative path 
	 * @return	string | false when param is invalid
	 */
	public function getPath($path = null, $isRelative = true)
	{
		$path = $this->validatePath($path, $isRelative);
		
		/*
		 * use the path as is
		 */
		if (false === $isRelative) {
			return $path;
		}

		$root = $this->getResolvedRootPath();
		if (empty($path)) {
			return $root;
		}
		else if (empty($root)) {
			return $path;
		}

		$sep  = DIRECTORY_SEPARATOR;
		$path = ltrim($path, "$sep");
		if ($sep === $root) {
			return "{$sep}{$path}";
		}

		return "$root{$sep}{$path}";
	}

	/**
	 * @param	string	$path
	 * @param	bool	$isRelative
	 * @return	bool
	 */
	public function fileExists($path = null, $isRelative = true)
	{
		if (file_exists($this->getPath($path, $isRelative))) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string $path
	 * @return	bool
	 */
	public function isWritable($path = null, $isRelative = true)
	{
		if (is_writable($this->getPath($path, $isRelative))) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isReadable($path = null, $isRelative = true)
	{
		if (is_readable($this->getPath($path, $isRelative))) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isFile($path = null, $isRelative = true)
	{
		if (is_file($this->getPath($path, $isRelative))) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isDir($path = null, $isRelative = true)
	{
		if (is_dir($this->getPath($path, $isRelative))) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isLink($path = null, $isRelative = true)
	{
		if (is_link($this->getPath($path, $isRelative))) {
			return true;
		}

		return false;
	}

	/**
	 * Validate the path to determine it is safe and normalize the directory
	 * separator to 
	 *
	 * @param	string	$path 
	 * @param	bool	$isRelative
	 * @return	string
	 */
	protected function validatePath($path = null, $isRelative = true)
	{	
		if (true === $isRelative && null === $path) {
			return '';
		}

		if (false === $isRelative && null === $path) {
			$err = 'absolute path must not be null';
			throw new DomainException($err);
		}
	
		if (! $this->isStringable($path)) {
			$err  = 'path must be null, string or an object that ';
			$err .= 'implements __toString';
			throw new DomainException($err);
		}

		/*
		 * ensure we convert any object to a string
		 */
		if (is_object($path)) {
			$path = (string) $path;
		}

		$path = trim($path, " \n\t");
		if ('' === $path) {	
			if (false === $isRelative) {
				$err = 'absolute path can not be empty';
				throw new DomainException($err);
			}
			
			return '';
		}

		/* root directory by itself is considered valid */
		$sep = DIRECTORY_SEPARATOR;
		if ($sep === $path) {
			return $path;
		}

		/*
		 * metadata that allows you to traverse up and down the path
		 * like ../../mypath, is not allowed because its usage is more 
		 * risk than its worth so finding it is considered malicious code
		 */
		if (false !== strpos($path, "..")) {
			$err = 'directory path meta data such as ".." is not allowed';
			throw new DomainException($err);
		}

		/**
		 * convert unix style separator to DIRECTORY_SEPARATOR
		 */
		$path = str_replace('/', $sep, $path);

		/*
		 * Ensure relative paths are contained within their root path
		 */
		if (true === $isRelative && 
			true === $this->isBasePath() &&
			false !== strpos($path, $this->getBasePath())) { 
			$err  = "path can not contain base path once it is defined";
			throw new DomainException($err);
		}

		return $path;
	}

	/**
	 * @param	string	$path
	 * @return	null
	 */
	protected function setBasePath($path)
	{
		$isRelative = false;
		$sep  = DIRECTORY_SEPARATOR;
		$this->basePath = rtrim($this->validatePath($path, $isRelative), $sep);
	}

	/**
	 * @param	mixed	$path
	 * @return	bool
	 */
	protected function isStringable($path)
	{
		if (is_string($path) ||
			(is_object($path) && is_callable(array($path, '__toString')))) {
			return true;
		}
	
		return false;
	}

	/**
	 * Checks for the forward slash on the relative root so you don't end
	 * up the a path that has double slashes.
	 * 
	 * @return	string
	 */
	protected function getResolvedRootPath()
	{
		$sep  = DIRECTORY_SEPARATOR;
		$root = $this->getRootPath();
		/* 
		 * when the base path is disabled we must preserve the absolute path
		 */
		if (! $this->isBasePath()) {
			return ($sep === $root) ? $root : rtrim($root, $sep);
		}

		$base = $this->getBasePath();
		return rtrim("$base/$root", " \n\t$sep");
	}
}
