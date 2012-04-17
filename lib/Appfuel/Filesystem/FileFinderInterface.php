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
interface FileFinderInterface
{
	/**
	 * Appfuel sets this in the constructor ensuring it is the same path
	 * as the constant AF_BASE_PATH. 
	 * 
	 * 1) An immutable string that represents the absolute path to the base
	 *    path of the application
	 * 
	 * @return string
	 */
	public function getBasePath();

	/**
	 * Becase the base path is optional this method exists to check 
	 * if it exits
	 *
	 * @return	bool
	 */
	public function isBasePath();

	/**
	 * @return	string
	 */
	public function getRootPath();

	/**
	 * When base path exists then the root path acts as a relative point
	 * to concatenate file paths onto. When base path is disabled root path
	 * will act as a base path. This allows the FileFinder to use file paths
	 * both in and out of the application path. 
	 *
	 * 1) $path must be a string or an object that implements __toString
	 *    When this criteria fails throw a DomainException
	 * 2) remove whitespaces from the $path
	 * 3) $path is allowed to be an empty string
	 * 4) remove DIRECTORY_SEPARATOR from the right side unless the only
	 *    character is the DIRECTORY_SEPARATOR ex) '/'
	 * 5) Ensure if base path exists then the root path does not include
	 *    base path. 
	 *    when this criteria fails throw a DomainExcepion
	 *
	 * @throws  DomainException
	 * @return	FileFinderInterface
	 */
	public function setRootPath($path);

	/**
	 * Creates an absolute path by resolving base path (when it exists) root
	 * path and the path passed in as a parameter. When $isRelative is false
	 * $path is considered an absolute path.
	 * 
	 * 1) $path can be null only when $isRelative is true
	 *    when outside this domain throw a DomainException
	 * 2) $path can be a string or an object that implements __toString
	 *    when outside this domain throw a DomainException
	 * 3) when an object $path is casted to a string
	 * 4) trim whitespaces from both left and right sides of $path
	 *	  if after whitespace have been removed and $isRelative is false and
	 *    $path is now empty throw a DomainException
	 * 5) metadata chars .. are not allowed in any paths because the pose a
	 *	  security risk when the are found throw an DomainException
	 * 6) when $isRelative is true and $path contains the root path 
	 *    throw an DomainException
	 * 7) when $isRelative is false and $path is '/' return path
	 * 8) when base path exists and root path exits resolve any conflicting
	 *    directory separator and concatenate them togather then concatenate
	 *    $path to the result and return. 
	 *  
	 * @throws	DomainException
	 * @param	string	$path
	 * @param	bool	$isRelative
	 * @return	string
	 */
	public function getPath($path = null, $isRelative = true);

	/**
	 * @param	string	$path
	 * @param	bool	$isRelative
	 * @return	bool
	 */
	public function fileExists($path = null, $isRelative = true);

	/**
	 * @param	string $path
	 * @return	bool
	 */
	public function isWriteable($path = null, $isRelative = true);

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isReadable($path = null, $isRelative = true);

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isFile($path = null, $isRelative = true);

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isDir($path = null, $isRelative = true);

	/**
	 * @param	string	$path
	 * @return	bool
	 */
	public function isLink($path = null, $isRelative = true);
}
