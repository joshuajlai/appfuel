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
     * FileType
     * The file type for the resulting config file
     * @var string
     */
    protected $fileType = 'php';

    /**
     * File Name
     * The resulting filename that the builder created.  This is for
     * external programs to inspect, not alter.
     * @var string
     */
    protected $fileName = null;

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
     * @param   string      $fileType
     * @return  ConfigBuilder
     */
    public function setFileType($fileType)
    {
        $validFileTypes = array(
            'php',
            'json',
        );
        if (! in_array($fileType, $validFileTypes)) {
            throw new DomainException(
                'Unsupported file type requested'
            );
        }
        $this->fileType = $fileType;
    }
    
    /**
     * @return  string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param   string      $fileName
     * @return  ConfigBuilder
     */
    protected function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return  string
     */
    public function getFileName()
    {
        return $this->fileName;
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
		return $reader->import($envFile, $isThrow);
	}

	/**
	 * @throws	RunTimeException
	 * @return	array
	 */
	public function getProductionData()
	{
		$reader  = $this->getFileReader();
		
		$isThrow = true;
		return $reader->import('production.php', $isThrow);
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
		$env = $this->getCurrentEnv();
		if ('production' === $env) {
			$data = $this->getProductionData();
		}
		$data = $this->mergeConfigurations();
        $type = $this->getFileType();
        switch ($type) {
            case 'json':
                $fileName   = 'config.json';
                $content    = $this->processJson($data);
                break;
            case 'php':
                $fileName   = 'config.php';
                $content    = $this->processPhp($data);
                break;
            default:
                throw new RuntimeException(
                    "Unexpected file type found during generate -($type)"
                );
        }

        $this->setFileName($fileName);
		$writer = $this->getFileWriter();
		return $writer->putContent($content, $fileName);
    }

    /**
     * Write the contents of the data array into a file as a php
     * formatted array.
     *
     * @param   array   $data
     * @return  bool
     */
    protected function processPhp(array $data)
    {
		$content = "<?php \n /* generated config file */ \n return ";
		$content .= $this->printArray($data);
		$content .= "\n?>";

        return $content;
	}

    /**
     * Write the contents of the data array into a file as a json formatted
     * object.
     *
     * @param   arary   $data
     * @return  bool
     */
    protected function processJson(array $data)
    {
        return json_encode($data);
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
