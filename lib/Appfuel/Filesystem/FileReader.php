<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
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
    public function __construct(FileFinderInterface $finder)
    {
		$this->setFileFinder($finder);
    }

	/**
	 * @return	FileFinderInterface
	 */
	public function getFileFinder()
	{
		return $this->finder;
	}

	public function setFileFinder(FileFinderInterface $finder)
	{
		$this->finder = $finder;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isBasePath()
	{
		return $this->getFileFinder()
					->isBasePath();
	}

	/**
	 * @return string | null when not set
	 */
	public function getBasePath()
	{
		return $this->getFileFinder()
					->getPathPath();
	}

	/**
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @return	bool
	 */
	public function import($path=null, $isThrow=false, $msg=null, $code=null)
	{
		$finder = $this->getFileFinder();
		$full   = $finder->getPath($path);
		if ($finder->fileExists($full, false)) {
			return require $full;
		}

		if (true === $isThrow) {
			$this->throwException($full, $msg, $code);
		}
		
		return false;
	}
	
	/**
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @param	int		$code
	 * @return	bool
	 */
	public function importOnce($path, $isThrow=false, $msg=null, $code=null)
	{
		$finder = $this->getFileFinder();
		$full   = $finder->getPath($path);
		if ($finder->fileExists($full, false)) {
			return require_once $full;
		}

		if (true === $isThrow) {
			$this->throwException($full, $msg, $code);
		}
		
		return false;
	}

	/**
	 * @param	string	$path
	 * @param	bool	$isAssoc
	 * @param	int		$depth
	 * @param	int		$options
	 * @return	array | object
	 */
	public function decodeJsonAt($path = null, $isAssoc = true, $depth = null)
	{
		$finder  = $this->getFileFinder();
		$content = $this->getContent($path);
		if (false === $content) {
			return false;
		}

		$assoc = true;
		if (false === $isAssoc) {
			$assoc = false;
		}

		if (null === $depth) {
			$depth = 512;
		}
		else if (! is_int($depth) || $depth < 0) {
			$err = "json depth must be a positive integer";
			throw new InvalidArgumentException($err);
		}

		return json_decode($content, $assoc, $depth);
	}

	/**
	 * @return	string
	 */
	public function getLastJsonError()
	{
		switch (json_last_error()) {
			case JSON_ERROR_DEPTH:
				$result = 'maximum stack depth exceeded';
				break;
			case JSON_ERROR_CTRL_CHAR:
				$result = 'unexpected control char found';
				break;
			case JSON_ERROR_SYNTAX:
				$result = 'syntax error, malformed JSON';
				break;
			case JSON_ERROR_NONE:
			default:
				$result = false;
		}
	
		return $result;	
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$path
	 * @param	bool	$isRel	is path relative or absolute
	 * @param	int		$offset
	 * @param	int		$max
	 * @return	string | false when does not exist
	 */
	public function getContent($path = null, $offset = null, $max = null)
	{
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

		$finder = $this->getFileFinder();
		$full   = $finder->getPath($path);
		if (! $finder->fileExists($full, false)) {
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
		if (! $finder->fileExists($full, false)) {
			return false;
		}

		return file($full, $flags);			
	}

	/**
	 * @param	string	
	 * @param	string
	 * @param	int	$code
	 * @return	null
	 */
	protected function throwException($path, $msg = null, $code = null)
	{
		$err = "require failed: file not found at -($path)";
		if (is_string($msg) && ! empty($msg)) {
			$err = $msg;
		}

		$eCode = 404;
		if (null !== $code) {
			$eCode = $code;
		}


		throw new RunTimeException($err, $eCode);
	}
}
