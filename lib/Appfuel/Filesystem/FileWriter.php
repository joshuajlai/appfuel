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
class FileWriter implements FileWriterInterface
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
	 * @param	string	$data
	 * @param	string	$path
	 * @param	int	$flags
	 * @return	int
	 */
	public function putContent($data, $path, $flags = 0)
	{
		$finder = $this->getFileFinder();
		$full = $finder->getPath($path);
		return file_put_contents($full, $data, $flags);
	}

	/**
	 * @param	string	$path 
	 * @param	int		$mode
	 * @param	bool	$isRecursive
	 * @return	
	 */
	public function mkdir($path, $mode = null, $isRecursive = null)
	{
		$recursive = false;
		if (true === $isRecursive) {
			$recursive = true;
		}

		$finder = $this->getFileFinder();
		$full   = $finder->getPath($path);
		return mkdir($full, $mode, $recursive);
	}
}
