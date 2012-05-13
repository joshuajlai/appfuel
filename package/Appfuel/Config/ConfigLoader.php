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

use DomainException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\Filesystem\FileReaderInterface;

/**
 * Loads config data into the configuration registry. The data can be from a
 * php file that returns an array or a json file, the data can also be just 
 * an array. A section can also be isolated and used as the config instead of
 * of the whole config. If section is used and an array key 'common' exists
 * the loader will try to merge common into the section. 
 */
class ConfigLoader implements ConfigLoaderInterface
{
	/**
	 * @var	FileReader
	 */
	protected $reader = null;

	/**
	 * @param	FileReaderInterface $reader
	 * @return	ConfigLoader
	 */
	public function __construct(FileReaderInterface $reader = null)
	{
		if (null === $reader) {
			$reader = new FileReader(new FileFinder());
		}

		$this->reader = $reader;
	}

	/**
	 * @return	FileReaderInterface
	 */
	public function getFileReader()
	{
		return $this->reader;
	}

	/**
	 * @param	string	$path
	 * @param	string	$path
	 * @return	bool
	 */
	public function loadFile($path, $isReplace = true)
	{
		$data = $this->getFileData($path);
		if (true === $isReplace) {
			$this->set($data);
			return;
		}
			
		$this->load($data, $section);
	}

	/**
	 * @param	mixed	$data
	 * @return	null
	 */
	public function load(array $data)
	{
		ConfigRegistry::load($data);
	}

	/**
	 * @param	array	$data
	 * @return	null
	 */
	public function set(array $data)
	{
		ConfigRegistry::setAll($data);
	}

	/**
	 * Read a json file or any php file. When php files are used it expects 
	 * the file will return an array of config data
	 *
	 * @param	string	$path	relative path to the config file
	 * @return	array
	 */
	public function getFileData($path)
	{
		if (! is_string($path) || empty($path)) {
			$err = "path to config must be a none empty string";
			throw new InvalidArgumentException($err);
		}

		$reader = $this->getFileReader();
		if (false !== strpos($path, '.json')) {
			$data = $reader->decodeJsonAt($path, true);
			if (! $data) {
				$full = $reader->getFileFinder()
							   ->getPath($path);
				$msg = $reader->getLastJsonError();
				$err = "could not load config file at -($full): $msg ";
				throw new RunTimeException($err);
			}
		}
		else {
			$data = $reader->import($path, true);
		}
		
		return $data;	
	}
}
