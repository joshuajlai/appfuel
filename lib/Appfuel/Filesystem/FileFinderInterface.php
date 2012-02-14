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
 * The file finder creates absolute paths with getPath. A path is broken up
 * into three sections
 *  
 * 1) base path: absolute path to the root directory of the application
 * 2) root path: relative path from the base path to the path specified in 
 *	  getPath. 
 * 3) relative path: relative path to file or directory given to getPath
 * 
 * Base, root and relative paths are concatenated to form the
 * absolute path to the file or directory. 
 * 
 * There is no interface for disabling the base path, this is an immutable 
 * property that should be implmented in the constructor. I feel adding the
 * constructor to the interface makes the design ridged so I leave it up to 
 * you to decide how to initialize the file finder. 
 */
interface FileFinderInterface
{
	/**
	 * 1) must be the same as AF_BASE_PATH
	 * 2) should be set in the constructor and made immutable
	 *	  (protected setter)
	 * 3) do not trim the base path this is done in resolveRoot
	 *
	 * @return string
	 */
	public function getBasePath();

	/**
	 * Flag used to determine if the base path is set.
	 * 1) constant AF_BASE_PATH must be defined AND
	 *    the string stored as base path must be equal to the constant
	 *
	 * @return	bool
	 */
	public function isBasePath();

	/**
	 * @return	string
	 */
	public function getRootPath();

	/**
	 * Root path acts as relative path when base path. When base path is 
	 * not used if effectively acts like base path. 
	 * 
	 * 1) $path must be a string or an object that supports __toString 
	 *	  anythingelse will throw an InvalidArgumentException
	 * 2) $path will be casted to a string for objects
	 * 3) when base path is used, $path can not contain base path
	 *	  throw a RunTimeException if it does
	 * 4) do not trim root path in any way here keep it original. All trim 
	 *    operations are done with resolveRootPath
	 *
	 * @throws	InvalidArgumentExceptions
	 * @throws	RunTimeException
	 * @param	string|object	$path 
	 * @return	FileFinder
	 */
	public function setRootPath($path);

	/**
	 * Resolve base path and root path into a single string.
	 * 
	 * trim chars include: spaces, new lines, tabs and forward slashes
	 * 
	 * 1) when base path is not used and root path is '/' return '/'
	 * 2) when base path is not used and root is not '/'  then
	 *    trim the right side of root with trim chars. 
	 * 3) always trim the right side of base for trim chars.
	 * 4) concatenate base and root with a forward slash then right trim the 
	 *    results with trim chars
	 * 
	 * @return	string
	 */
	public function getResolvedRootPath();

	/**
	 * Create an absolute path with the path given plus base path and root path
	 * when necessary
	 *
	 * trim chars include: spaces, new lines, tabs and forward slashes
	 *
	 * 1) If no path is given then path is an empty string
	 * 2) A valid path is a string or an object that implements __toString
	 *	  anything is should return false
	 * 3) cast path into string
	 * 4) resolve the root path using 'getResolvedRootPath'
	 * 5) when path is empty return the resolved root
	 * 6) when the resolved root is empty return path
	 * 7) concatenate root and path with a forward slash
	 *
	 * @param	string	$path	relative path 
	 * @return	string
	 */
	public function getPath($path = null);

	/**
	 * Resolve the path into an absolute and then check its existence
	 * @param	string $path
	 * @return	bool
	 */
	public function pathExists($path = null);

	/**
	 * @param	string $path
	 * @return	bool
	 */
	public function fileExists($full);

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
}
