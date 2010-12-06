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
	 * Used to determine the configuration strategy based on type of file
	 * @var string
	 */
	protected $fileStrategy = 'ini';

	/**
	 * The current section this config will represent
	 * @var string
	 */
	protected $section = 'default';


	/**
	 * @param	string	$fileType	default config file is ini
	 * @return	Builder
	 */	
	public function __construct($fileType = 'ini')
	{
		$this->setFileStrategy($fileType);
	}

	/**
	 * @param	string	$section	section used in the config file
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
	 * @param	bool	$flag
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
	 * @return	AdapterInterface
	 */
	public function	getFileAdapter()
	{
		return $this->adapter;
	}

	/**
	 * @param	Adapter\AdapterInterface	$adapter
	 * @return	Builder
	 */	
	public function setFileAdapter(Adapter\AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
		return $this;
	}

	/**
	 * @return	bool
	 */ 
	public function isFileAdapter()
	{
		return $this->adapter instanceof Adapter\AdapterInterface;
	}

	/**
	 * @return	string
	 */
	public function	getFileStrategy()
	{
		return $this->fileStrategy;
	}

	/**
	 * This method will create a file adapter for the given file type.
	 * 
	 * @throws	Exception	when adapter does not exist
	 * @param	string	$fileType	type of file the config is ex) ini, xml
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
	public function	getSection()
	{
		return $this->section;
	}

	/**
	 * @return	Builder
	 */	
	public function setSection($name)
	{
		$this->section = $name;
		return $this;
	}


	/**
	 * Create a list object from the configuration array produced by the
	 * file adapter create when setFileStrategy is used. The default strategy
	 * is an ini file
	 *
	 * @return Appfuel\StdLib\Ds\AfList\Basic
	 */
	public function build(File $file)
	{
		$adapter = $this->getFileAdapter();
		if (! $this->isFileAdapter()) {
			throw new Exception(
				"A correct file strategy has not set or the file adapter has
				not manually been set"
			);  
		}
	
		$data    = $adapter->parse($file);
		$section = $this->getSection();

		if (! array_key_exists($section, $data)) {
			throw new Exception(
				'Can not build a config from a section that does not exist'
			);
		}

		if ($this->isInheritance()) {

			$isection = $this->getInheritSection();
			if (! array_key_exists($isection, $data)) {
				throw new Exception(
					'Can not inherit from a section that does not exist'
				);
			}
			$data = array_merge($data[$isection], $data[$section]);

		} else {
			$data = $data[$section];
		}

			
		return Factory::createConfigList($data);

	}

}
