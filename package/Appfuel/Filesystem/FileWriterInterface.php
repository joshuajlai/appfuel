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

/**
 * Reads the contents of a file into memory
 */
interface FileWriterInterface
{
	/**
	 * @return	FileFinderInterface
	 */
	public function getFileFinder();

	/**
	 * @param	FileFinderInterface		$finder
	 * @return	FileReader
	 */
	public function setFileFinder(FileFinderInterface $finder);

	/**
	 * @param	string	$data
	 * @param	string	$path
	 * @param	string	$mode
	 * @param	int		$length
	 * @return	
	 */
	public function putContent($data, $path, $flags = 0);
}
