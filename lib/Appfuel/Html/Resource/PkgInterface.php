<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Html\Resource;

use InvalidArgumentException;

/**
 * A package resource is a low level clientside resource. It has no markup
 * only js or css files. It has the following structure: derived from a 
 * json object whose objects where transformed into php associative arrays
 * 
 *  name: (string)	name of the package used to identify it. must be unique
 *					in the pkg namespace. required
 *  type: (string)  type of package which is always (pkg) other types include
 *					page, config, and view. required.
 *  desc: (string)  short description of what the package does. optional
 *  path: (string)  relative path from the pkg directory. optional
 *  src:  (array)   contains info about the source files and directory
 *		dir:	(string)	name of the dir holding the source files. optional
 *		files:	(array)		list of files keyed by type. required.
 *							example) js => array(file1,file2...)
 *  require: (array)	list of pkg names this pkg is dependent on.
 *
 * The recommended implementation pattern for this is a value object. I pass
 * in an array to the constructor and from there use a series of protected 
 * setters to initialize the object.
 *
 * Rules:
 *		when any value is set but is not the expected type throw an
 *      InvalidArgumentException.
 *
 *		name, type: when ommited throw DomainException. only non empty strings.
 *		desc:		when set must be string
 *		path:		when ommited the path will be '<name>' 
 *		srcDir:		when ommited will be 'src'. must be a string, can be empty.
 *		srcPath:	is the relative path to the src dir include the pkg dir.
 *					this is dervied not set. pkg/<path>/<srcDir>
 *		files		all files must be non empty strings when set.
 */
interface PkgInterface
{
	/**
	 * @return	string
	 */
	public function getName();

	/**
	 * @return	string
	 */
	public function getType();

	/**
	 * @param	string	$type	name of the type
	 * @return	bool
	 */
	public function isType($type);

	/**
	 * @return	string
	 */
	public function getDescription();

	/**
	 * @return	string
	 */
	public function getPath();

	/**
	 * @return	string
	 */
	public function getSourcePath();

	/**
	 * @return	string
	 */
	public function getSourceDirectory();

	/**
	 * @return	array
	 */
	public function getFileTypes();

	/**
	 * @return	string
	 */
	public function getAllFiles();

	/**
	 * @params	string $type 
	 * @return	array|false
	 */
	public function getFiles($type, $path = null);

	/**
	 * @return	bool
	 */
	public function isRequiredPackages();

	/**
	 * @return	array
	 */
	public function getRequiredPackages();
}
