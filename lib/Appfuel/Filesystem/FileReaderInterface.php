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
interface FileReaderInterface
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
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @return	bool
	 */
	public function requireFile($path, $isThrow=true, $msg='');
	
	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$path
	 * @param	int		$offset
	 * @param	int		$max
	 * @return	string | false when does not exist
	 */
	public function getContent($path, $isRel=true, $offset=null, $max=null);

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$file
	 * @param	int		$flags = 0
	 * @return	array | false when not found
	 */
	public function getContentAsArray($file, $flags=0);

	/**
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @return	bool
	 */
	public function requireOnceFile($path, $isThrow=true, $msg='');

	/**
	 * @param	string
	 * @return	mixed
	 */
	public function includeFile($path);

	/**
	 * @param	string
	 * @return	mixed
	 */
	public function includeOnceFile($path);
}
