<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Html\Resource;

use InvalidArgumentException;

/**
 * Holds a list of files categorized by type
 */
interface FileStackInterface
{
	/**
	 * 1) return an array of all files 
	 * 2) the array must be associative where the key is the file type string
	 *    and the value is a list of files for that type
	 * 3) when empty return an empty array
	 * 
	 * @return	array
	 */
	public function getAll();

	/**
	 * 1) return an array of file type strings used in the list
	 * 2) when no types are available return an empty array
	 *    (note: I use array_keys here)
	 *
	 * @return	array
	 */
	public function getTypes();

	/**
	 * 1) when the file type exists return the list of files for that type
	 * 2) when $type is not a string or does not match any file type keys 
	 *	  return false
	 *
	 * @param	string	$type
	 * @return	array | false when type does not exist
	 */
	public function get($type);

	/**
	 * 1) if $type is not a string or empty throw an InvalidArgumentException
	 * 2) if $file is not a string or empty throw an InvalidArgumentException
	 * 3) if $type has never been added then add it to the data structure and 
	 *    add that file as an array of one item
	 * 4) if the $type exists then append $file to its array
	 * 5) return PackageFileListInterface
	 *
	 * @throws	InvalidArgumentException
	 * @param	string	$type
	 * @param	string	$file
	 * @return	PackageFileList
	 */
	public function add($type, $file);

	/**
	 * 1) when not an associtive arrays throw an InvalidArgumentException
	 * 2) foreach file in the list key is type and value is string or array
	 *		a) if file is a string then delegate to 'add'
	 *		b) if file is an array loop each file and delegate to 'add'
	 *      c) if file is any other value throw new InvalidArgumentException
	 * 3) return PackageFileListInterface
	 *
	 * @param	array	$list
	 * @return	PackageFileListInterface
	 */
	public function load(array $list);

	/**
	 * 1) delegate to 'clear' using null (clear all files)
	 * 2) delegate to 'load'
	 * 3) return PackageFileListInterface
	 *
	 * @param	array	$list
	 * @return	PackageFileListInterface
	 */
	public function set(array $list);

	/**
	 * 1) if type is null clear the whole data structure
	 * 2) if type is not a string or does not match any type return 
	 *    PackageFileListInterface
	 * 3) assign an empty array associated to that type
	 *
	 * @return	PackageFileLIstInterface
	 */
	public function clear($type = null);
}
