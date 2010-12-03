<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package 	Appfuel
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace 	Appfuel\StdLib\Config;

use Appfuel\StdLib\Filesystem\File as File;

/**
 *
 * @package 	Appfuel
 */
class Builder implements BuilderInterface
{

	/**
	 * The current section in the ini file will inherit this section
	 * @var	string
	 */ 	
	protected $inherit = 'production';

	/**
	 * Flag to determine if the builder will use inheritence in the ini file
	 * @return	bool
	 */ 
	protected $isInheritance = TRUE;

	/**
	 * Flag to determine if we are checking datatype. A hint is located in the label
	 * and has the form of bool:label. When enabled the builder will parse the label 
	 * and use the hint to cast the value
	 * @var
	 */
	protected $isDatatypeHint = TRUE;

	/**
	 * Path that points to the config file
	 * @var string
	 */
	protected $filePath = NULL;

	/**
	 * Used to determine the configuration strategy based on type of file
	 * @var string
	 */
	protected $fileStrategy = 'ini';


	/**
	 * @return	Builder
	 */	
	public function __construct($fileType = 'ini')
	{
		$this->setFileStrategy($fileType);
	}

	/**
	 * @return	Builder
	 */
	public function	inherit($section = 'production')
	{
		$this->inherit = $section;
		return $this;
	}

	/**
	 * @return	string
	 */ 
	public function getInheritSection()
	{
		return $this->inherit;
	}

	/**
	 * @return	Builder
	 */
	public function	disableInheritance()
	{
		return $this->setInheritanceFlag(FALSE);
	}

	/**
	 * @return	Builder
	 */
	public function	enableInheritance()
	{
		return $this->setInheritanceFlag(TRUE);
	}

	/**
	 * @return	Builder
	 */
	public function	setInheritanceFlag($flag)
	{
		$this->isInheritance = (bool) $flag;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function	isInheritance()
	{
		return $this->isInheritance;
	}

	/**
	 * @return	Builder
	 */
	public function	enableDatatypeHint()
	{
		return $this->setDatatypeHintFlag(TRUE);
	}

	/**
	 * @return	Builder
	 */
	public function	disableDatatypeHint()
	{
		return $this->setDatatypeHintFlag(FALSE);
	}

	/**
	 * @return	Builder
	 */
	public function	setDatatypeHintFlag($flag)
	{
		$this->isDatatypeHint = (bool) $flag;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function	isDatatypeHint()
	{
		return $this->isDatatypeHint;
	}

	/**
	 * @return	AdapterInterface
	 */
	public function	getFileAdapter()
	{
		return $this->adapter;
	}

	/**
	 * @return	Builder
	 */	
	public function setFileAdapter(Adapter\AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function	getFileStrategy()
	{
		return $this->fileStrategy;
	}

	/**
	 * @return	Builder
	 */	
	public function setFileStrategy($fileType)
	{
		$this->fileStrategy = $fileType;
		$this->setFileAdapter(Factory::createAdapter($fileType));
		return $this;
	}

	/**
	 * @return	string
	 */
	public function	getFilePath()
	{
		return $this->filePath;
	}

	/**
	 * @return	Builder
	 */	
	public function setFilePath($path)
	{
		$this->filePath = $path;
		return $this;
	}


	/**
	 * @return	NULL
	 */
	public function build($file = NULL)
	{
		$file = $this->processFile($file);
		if (! $file instanceof File) {
			$type = "\Appfuel\StdLib\\Filesystem\\File";
			throw new Exception("File must be of type $type");  
		}

		$adapter = $this->getFileAdapter();
		if (! $adapter instanceof Adapter\AdapterInterface) {
			$type = __NAMESPACE__ . "\\Adapter\\AdapterInterface";
			throw new Exception("File adapter must be of type $type");  
		}
	
		$data = $adapter->parse($file);
		echo "\n", print_r($data,1), "\n";exit; 
	}

	protected function processFile($file)
	{
		if ($file instanceof File) {
			return $file;
		} 

		$isString      = is_string($file);
		$isEmpty	   = empty($file);
		$isValidString = is_string($file) && ! $isEmpty;

		if ($isValidString) {
			return Factory::createFile($file);
		}

		$file = $this->getFilePath();
		$isValidString = is_string($file) && ! $isEmpty;
		if ($isValidString) {
			return Factory::createFile($file);
		}

		throw new Exception('failed: no filepath was set or given');
	}
}

