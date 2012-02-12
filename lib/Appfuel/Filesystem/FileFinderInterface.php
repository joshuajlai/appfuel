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
 * The file finder is used to encapsulate the knowledge of the base path and
 * whatever relative path the developer specifies in the constructor. This 
 * allows the code to resolve absolute paths without ever caring about what
 * they are or how to build them. They emphsis is on the more portable 
 * relative path.
 */
interface FileFinderInterface
{

	/**
	 * @return string
	 */
	public function getBasePath();

	/**
	 * @return	string
	 */
	public function getRelativeRootPath();

	/**
	 * @throws  RunTimeException
	 * @throws	InvalidArgumentException
	 * @return	PathFinder
	 */
	public function setRelativeRootPath($path);

	/**
	 * Returns an absolute path to with the specified path appended to it
	 * 
	 * @param	string	$path	relative path 
	 * @return	string
	 */
	public function getPath($path = null);

	/**
	 * @param	string $path
	 * @return	bool
	 */
	public function fileExists($path = null);

	/**
	 * @param	string $path
	 * @return	bool
	 */
	public function isWriteable($path = null);

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isReadable($path = null);

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isFile($path = null);

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isDir($path = null);

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isLink($path = null);

	/**
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @return	bool
	 */
	public function requireFile($path, $throw = true, $msg = '');

	/**
	 * @param	string	$path
	 * @param	bool	$throw
	 * @param	string	$msg
	 * @return	bool
	 */
	public function requireOnceFile($path, $isThrow = true, $msg = '');

	/**
	 * @param	string
	 * @return	mixed | false on failure
	 */
	public function includeFile($path);
	
	/**
	 * @param	string
	 * @return	mixed | false on failure
	 */
	public function includeOnceFile($path);


}
