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
namespace Appfuel\Kernel;

use InvalidArgumentException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\Filesystem\FileWriter,
	Appfuel\Filesystem\FileFinderInterface,
	Appfuel\Filesystem\FileReaderInterface,
	Appfuel\Filesystem\FileWriterInterface;

/**
 * Build a config file from merging two enviroment specific config files 
 * togather
 */
class ConfigBuilder implements ConfigBuilderInterface
{
	/**
	 * Used to find the config files on disk
	 * @var FileFinderInterface
	 */
	protected $fileFinder = null;

	/**
	 * Used to read the config file data
	 * @var FileReaderInterface
	 */
	protected $fileReader = null;

	/**
	 * Used to write the final config file
	 * @var FileWriterInterface 
	 */
	protected $fileWriter = null;

	/**
	 * Current environment config we are building for
	 * @var string
	 */
	protected $currentEnv = ' ';
	
	/**
	 * Env we will be merge to
	 * @var string
	 */
	protected $mergeEnv = 'prod';

	/**
	 * @param	string	$env
	 * @param	FileFinderInterface	$finder
	 * @param	FileReaderInterface $reader
	 * @param	FileWriterInterface $writer
	 * @return	ConfigBuilder
	 */
	public function __construct($env, 
								FileFinderInterface $finder = null,
								FileReaderInterface $reader = null,
								FileWriterInterface $writer = null)
	{
		$this->setCurrentEnv($env);
		if (null === $finder) {
			$finder = new FileFinder('app/config');
		}
		$this->finder = $finder;

		if (null === $reader) {
			$reader = new FileReader($this->finder);
		}
		$this->reader = $reader;

		if (null === $writer) {
			$writer = new FileWriter($this->finder);
		}
		$this->writer = $writer;
	}

	/**
	 * @return	string
	 */
	public function getMergeEnv()
	{
		return $this->mergeEnv;
	}

	/**
	 * @return string
	 */
	public function getCurrentEnv()
	{
		return $this->currentEnv;
	}

	/**
	 * @param	string	$char
	 * @return	ConfigBuilder
	 */
	public function setCurrentEnv($env)
	{
		if (! is_string($env) || empty($env)) {
			$err = 'current environment name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->currentEnv = $env;
		return $this;
	}

	/**
	 * @return	FileFinderInterface
	 */
	public function getFileFinder()
	{
		return $this->finder;
	}

	/**
	 * @return	FileReaderInterface
	 */
	public function getFileReader()
	{
		return $this->reader;
	}

	/**
	 * @return	FileWriterInterface
	 */
	public function getFileWriter()
	{
		return $this->writer;
	}

	/**
	 * @throws	RunTimeException
	 * @return	array
	 */
	public function getCurrentEnvData()
	{
		$reader  = $this->getFileReader();
		$env     = $this->getCurrentEnv();
		$envFile = "$env.php";
		
		$isThrow = true;
		return $reader->requireFile($envFile, $isThrow);
	}

	/**
	 * @throws	RunTimeException
	 * @return	array
	 */
	public function getProductionData()
	{
		$reader  = $this->getFileReader();
		
		$isThrow = true;
		return $reader->requireFile('prod.php', $isThrow);
	}

	/**
	 * @return	array
	 */
	public function mergeConfigurations()
	{
		$prod = $this->getProductionData();
		$env  = $this->getCurrentEnvData();
		return array_replace_recursive($prod, $env);
	}

	/**
	 * @return	string
	 */
	public function generateConfigFile()
	{
		$data = $this->mergeConfigurations();
		$env = $this->getCurrentEnv();
		$data['env'] = $env;
	
		$content = "<?php \n /* generated config file */ \n return ";
		$content .= $this->printArray($data);
		$content .= "?>";
		$writer = $this->getFileWriter();
		return $writer->putContent($content, 'app-config.php');
	}

	/**
	 * @param	array	$array
	 * @return	string
	 */
	public function printArray(array $array)
	{
		$str  = "array(\n";
		$str .= $this->printArrayBody($array);
		$str .= ");";
		return $str;
	}

	/**
	 * @param	array	$array
	 * @param	int		$level
	 * @return	string
	 */	
	public function printArrayBody(array $array, $level = 0)
	{
		$tab = str_repeat("\t", $level);
		$body = '';
		foreach ($array as $key => $value) {
			
			$type = gettype($value);
			switch ($type) {
				case 'object':
					continue 2;
					break;
				case 'boolean':
					$vline = (true ===$value) ? "true" : "false";
					$vline .= ",\n";
					break;
				case 'integer':
				case 'double':
					$vline = "$value,\n";
					break;
				case 'string':
					$vline = "'{$value}', \n";
					break;
				case 'array':
					$vline = "array(\n" . 
							 $this->printArrayBody($value, $level+1) .
							 "$tab),\n";
					break;
			}
			$kline = (is_string($key)) ? "{$tab}'{$key}'" : $tab . $key;
			$body .= $kline . ' => ' . $vline;	
		}

		return $body;
	}


}
