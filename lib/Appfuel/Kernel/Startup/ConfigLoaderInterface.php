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
namespace Appfuel\Kernel\Startup;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\Filesystem\FileReaderInterface;

/**
 * Loads config data into the configuration registry. The data can be from a
 * php file that returns an array or a json file, the data can also be just 
 * an array.
 */
interface ConfigLoaderInterface
{
	/**
	 * @return	FileReaderInterface
	 */
	public function getFileReader();

	/**
	 * @param	string	$path
	 * @param	string	$path
	 * @return	bool
	 */
	public function loadFile($path, $section = null, $isReplace = true);

	/**
	 * @param	array $data
	 * @return	null
	 */
	public function load(array $data);

	/**
	 * @param	array	$data
	 * @return	null
	 */
	public function set(array $data);

	/**
	 * @param	array	$data
	 * @param	string	$name
	 * @return	null
	 */
	public function getSection(array $data, $name);

	/**
	 * @param	string	$path
	 * @return	array | false
	 */
	public function getFileData($path);
}
