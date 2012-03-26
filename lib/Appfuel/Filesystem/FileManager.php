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
 * Encapsulates all file system operation. Facade for the file finder, reader,
 * and writer.
 */
class FileManager implements FileManagerInterface
{
	/**
	 * @var	FileFinderInterface 
	 */
	protected $finder = null;

	/**
	 * @var FileReaderInterface
	 */
	protected $reader = null;

	/**
	 * @var FileWriterInterface
	 */
	protected $writer = null;

    /**
     *  
     * @param	FileFinderInterface 
	 * @param	FileReaderInterface
	 * @param	FileWriterInterface
     * @return  FileReader
     */
    public function __construct(FileFinderInterface $finder = null,
								FileReaderInterface $reader = null,
								FileWriterInterface $writer = null)
    {
		if (null === $finder) {
			$finder = new FileFinder();
		}
		$this->setFileFinder($finder);

		if (null === $reader) {
			$reader = new FileReader($finder);
		}
		$this->setFileReader($reader);

		if (null === $writer) {
			$writer = new FileWriter($finder);
		}
		$this->setFileWriter($writer);
    }

	/**
	 * @return	string
	 */
	public function getBasePath()
	{
		return $this->getFileFinder()
					->getBasePath();
	}

	/**
	 * @throws	RunTimeException
	 * @throws	InvalidArgumentException
	 * @param	string	$path
	 * @return	FileManager
	 */
	public function setRootPath($path)
	{
		$this->getFileFinder()
			 ->setRootPath($path);

		return $this;
	}

	/**
	 * @param	string	$path
	 * @return	string
	 */
	public function getPath($path = null, $isRelative = true)
	{
		return $this->getFileFinder()
					->getPath($path);
	}

	/**
	 * @param	string	$path
	 * @param	bool	$isRelative
	 * @return	bool
	 */
	public function fileExists($path, $isRelative = true)
	{
		return $this->getFileFinder()
				    ->fileExists($path, $isRelative);
	}

	/**
	 * @paam	string	$path	
	 * @param	bool	$isRelative
	 * @return	bool
	 */
	public function isWritable($path, $isRelative = true)
	{
		return $this->getFileFinder()
					->isWritable($path, $isRelative);
	}

	/**
	 * @paam	string	$path	
	 * @param	bool	$isRelative
	 * @return	bool
	 */
	public function isReadable($path, $isRelative = true)
	{
		return $this->getFileFinder()
					->isReadable($path, $isRelative);
	}

	/**
	 * @paam	string	$path	
	 * @param	bool	$isRelative
	 * @return	bool
	 */
	public function isFile($path, $isRelative = true)
	{
		return $this->getFileFinder()
					->isFile($path, $isRelative);
	}

	/**
	 * @paam	string	$path	
	 * @param	bool	$isRelative
	 * @return	bool
	 */
	public function isDir($path, $isRelative = true)
	{
		return $this->getFileFinder()
					->isDir($path, $isRelative);
	}

	/**
	 * @paam	string	$path	
	 * @param	bool	$isRelative
	 * @return	bool
	 */
	public function isLink($path, $isRelative = true)
	{
		return $this->getFileFinder()
					->isLink($path, $isRelative);
	}

	/**
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @param	int		$code
	 * @return	bool
	 */
	public function require($path, $isThrow = false, $msg = '', $code = 404)
	{
		return $this->getFileReader()
					->require($path, $isThrow, $msg, $code);
	}

	/**
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @return	bool
	 */
	public function requireOnce($path, $isThrow = false, $msg = '', $code = 404)
	{
		return $this->getFileReader()
					->requireOnce($path, $isThrow, $msg, $code);
	}

	public function decodeJsonAt($path, $isAssoc=true, $depth=null, $opt= null)
	{
		return $this->getFileReader()
					->decodeJsonIn($path, $isAssoc, $depth, $opt);
	}
	
	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$path
	 * @param	int		$offset
	 * @param	int		$max
	 * @return	string | false when does not exist
	 */
	public function getContent($path, $offset = null, $max = null)
	{
		return $this->getFileReader()
					->getContent($path, $offset, $max);
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$file
	 * @param	int		$flags = 0
	 * @return	array | false when not found
	 */
	public function getContentAsArray($path, $flags = 0)
	{
		return $this->getFileReader()
					->getContentAsArray($path, $flags);
	}

	/**
	 * @param	FileFinderInterface		$finder
	 * @return	null
	 */
	protected function setFileFinder(FileFinderInterface $finder)
	{
		$this->finder = $finder;
	}

	/**
	 * @param	FileFinderInterface		$finder
	 * @return	null
	 */
	protected function setFileReader(FileReaderInterface $reader)
	{
		$this->reader = $reader;
	}

	/**
	 * @param	FileWriterInterface		$finder
	 * @return	null
	 */
	protected function setFileReader(FileWriterInterface $writer)
	{
		$this->writer = $writer;
	}




}
