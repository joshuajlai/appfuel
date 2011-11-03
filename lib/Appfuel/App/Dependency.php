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
namespace Appfuel\App;

/**
 * Loads all required files into memory. This is used during initialization
 * before the autoloader is in place.
 */
class Dependency
{
	/**
	 * List of the necessary files before autoloading
	 * @var array
	 */
	protected $files = array();

	/**
	 * add the base path to complete the absolute path to each 
	 * necessary file then assign the files. must take into account
	 * that from the base path all php classes are located in the lib 
	 * folder.
	 *
	 * @param	string	$libDir	root path to appfuel classes 
	 * @return	Dependency
	 */
	public function __construct($libDir)
	{
		$path = "{$libDir}/Appfuel";
		$app  = "{$path}/App";
		$fw   = "{$path}/Framework";

		$this->files = array(
			"{$fw}/Exception.php",
			"{$fw}/DataStructure/DictionaryInterface.php",
			"{$fw}/DataStructure/Dictionary.php",
			"{$fw}/Registry.php",
			"{$fw}/File/FileManager.php",
			"{$fw}/Env/ErrorDisplay.php",
			"{$fw}/Env/ErrorReporting.php",
			"{$fw}/Env/AutoloadInterface.php",
			"{$fw}/Env/Autoloader.php",
			"{$fw}/Env/IncludePath.php",
			"{$fw}/App/Init/InitializerInterface.php",
			"{$fw}/App/Init/TaskInterface.php",
			"{$fw}/App/AppFactoryInterface.php",
			"{$app}/Init/Initializer.php",
			"{$app}/Init/SystemTask.php",
			"{$app}/Init/DbTask.php",
			"{$app}/AppFactory.php",
		);
	}

	/**
	 * @return array
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * These are dependencies, operations can not continue without
	 * them, we fail hard because there is no point continuing 
	 * 
	 * @throws	\Exception
	 * @param	array	$files
	 * @return	null
	 */
	public function requireFiles(array $files)
	{
		foreach ($files as $file) {
			if (! file_exists($file)) {
				throw new \Exception(
					"Required file could not be found $file"
				);
			}

			require_once $file;
		}
	}

	/**
	 * get and require the dependent files
	 *
	 * @return	null
	 */
	public function load()
	{
		$this->requireFiles($this->getFiles());
	}
}
