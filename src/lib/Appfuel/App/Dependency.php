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
		$sep  = DIRECTORY_SEPARATOR;
		$path = "{$libDir}{$sep}Appfuel{$sep}";
		$app  = "{$path}App{$sep}";
		$fw   = "{$path}Framework{$sep}";
		$std  = "{$path}Stdlib{$sep}";

		$this->files = array(
			"{$fw}Exception.php",
			"{$std}Data{$sep}BagInterface.php",
			"{$std}Data{$sep}Bag.php",
			"{$std}Filesystem{$sep}Manager.php",
			"{$std}Filesystem{$sep}File.php",
			"{$fw}Env{$sep}Initializer.php",
			"{$fw}Env{$sep}ErrorDisplay.php",
			"{$fw}Env{$sep}ErrorReporting.php",
			"{$fw}Env{$sep}AutoloadInterface.php",
			"{$fw}Env{$sep}Autoloader.php",
			"{$fw}Env{$sep}Timezone.php",
			"{$fw}Env{$sep}IncludePath.php",
			"{$app}Factory.php",
			"{$path}Registry.php",
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
