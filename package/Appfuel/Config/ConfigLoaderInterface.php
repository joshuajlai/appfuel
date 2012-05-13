<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Config;

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
	public function loadFile($path, $isReplace = true);

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
	 * @param	string	$path
	 * @return	array | false
	 */
	public function getFileData($path);
}
