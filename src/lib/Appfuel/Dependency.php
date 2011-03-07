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
namespace Appfuel;

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
	 * necessary file then assign the files
	 *
	 * @param	string	$base	root path of the application
	 * @return	Dependency
	 */
	public function __construct($base)
	{
		$sep  = DIRECTORY_SEPARATOR;
		$path = "{$base}{$sep}Appfuel{$sep}";
		$fw   = "{$path}Framework{$sep}";
		$std  = "{$path}Stdlib{$sep}";

		$this->files = array(
			"{$path}Exception.php",
			"{$fw}Exception.php",
			"{$fw}StartupFactoryInterface.php",
			"{$fw}AutoloaderInterface.php",
			"{$fw}Init{$sep}Exception.php",
			"{$fw}Init{$sep}InitInterface.php",
			"{$fw}Init{$sep}Config.php",
			"{$fw}Init{$sep}Includepath.php",
			"{$fw}Init{$sep}Autoload.php",
			"{$std}Exception.php",
			"{$std}Autoload{$sep}Exception.php",
			"{$std}Autoload{$sep}Autoloader.php",
			"{$std}Filesystem{$sep}Exception.php",
			"{$std}Filesystem{$sep}Manager.php",
			"{$std}Filesystem{$sep}File.php",
			"{$path}App.php",
			"{$path}AppBuilder.php",
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
