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
 * Reads the contents of a file into memory
 */
class FileReader implements FileReaderInterface
{
	/**
	 * @var	FileFinderInterface 
	 */
	protected $finder = null;

    /**
     *  
     * @param	FileFinderInterface 
     * @return  FileReader
     */
    public function __construct(FileFinderInterface $finder = null)
    {
		if (null === $finder) {
			$finder = new FileFinder();
		}
		$this->setFileFinder($finder);
    }

	/**
	 * @return	FileFinderInterface
	 */
	public function getFileFinder()
	{
		return $this->finder;
	}

	/**
	 * @param	FileFinderInterface		$finder
	 * @return	FileReader
	 */
	public function setFileFinder(FileFinderInterface $finder)
	{
		$this->finder = $finder;
		return $this;
	}

	/**
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @return	bool
	 */
	public function requireFile($path, $isThrow = false, $msg = '')
	{
		$finder = $this->getFileFinder();
		$full = $finder->getPath($path);
		if ($finder->fileExists($full)) {
			return require $full;
		}

		if (false === $isThrow) {
			return false;
		}
		
		$err = "require failed: file not found at -($full)";
		if (is_string($msg) && ! empty($msg)) {
			$err = $msg;
		}

		throw new RunTimeException($err);
	}
	
	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$path
	 * @param	bool	$isRel	is path relative or absolute
	 * @param	int		$offset
	 * @param	int		$max
	 * @return	string | false when does not exist
	 */
	public function getContent($path, $isRel=true, $offset=null, $max=null)
	{
		$isRel = (false === $isRel) ? false : true;

		$err = 'failed to get file contents: ';
		if (null !== $offset && ! is_int($offset) || $offset < 0) {
			$err .= 'offset must be a int that is greater than zero';
			throw new InvalidArgumentException($err);
		}

		if (null !== $max && ! is_int($max) || $max < 0) {
			$err .= 'max must be a int that is greater than zero';
			throw new InvalidArgumentException($err);
		}

		if ($offset > $max) {
			$err .= 'offset can not be larger than max';
			throw new InvalidArgumentException($err);
		}

		
		$full   = $path;
		$finder = $this->getFileFinder();
		if (true === $isRel) {
			$full = $finder->getPath($path);
		}

		if (! $finder->fileExists($full)) {
			return false;
		}

		/*
		 * file_get_contents will return an empty string if the last param is
		 * null
		 */
		if (null === $max) {
			return file_get_contents($full, false, null, $offset);
		}
		
		return file_get_contents($full, false, null, $offset, $max);
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$file
	 * @param	int		$flags = 0
	 * @return	array | false when not found
	 */
	public function getContentAsArray($file, $flags = 0)
	{
		if (! is_int($flags)) {
			$err = 'failed to get file contents: flags must be an int';
			throw new InvalidArgumentException($err);
		}

		$finder = $this->getFileFinder();
		$full   = $finder->getPath($path);
		if (! $finder->fileExists($path)) {
			return false;
		}

		return file($full, $flags);			
	}

	/**
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @return	bool
	 */
	public function requireOnceFile($path, $isThrow = true, $msg = '')
	{
		$finder = $this->getFileFinder();
		$full = $finder->getPath($path);
		if ($finder->fileExists($full)) {
			return require_once $full;
		}

		if (false === $isThrow) {
			return false;
		}
		
		$err = "require_once failed: file not found at -($full)";
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
		$finder = $this->getFileFinder();
		$full   = $finder->getPath($path);
		if ($finder->fileExists($full)) {
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
		$finder = $this->getFileFinder();
		$full   = $finder->getPath($path);
		if ($finder->fileExists($full)) {
			return include_once $full;
		}
	
		return false;
	}
}
