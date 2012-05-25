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
namespace Appfuel\Validate;

use DomainException,
    InvalidArgumentException,
	Appfuel\Kernel\StartupTask,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader;

/**
 * Locate the validation map and added it to the validation factory
 */
class ValidationStartupTask extends StartupTask
{
	/**
	 * @return ValidationStartupTask
	 */
	public function __construct()
	{
		$this->setRegistryKeys(array('validation-file' => null));
	}
	
    /**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		$file = 'app/validation-map.php';
		if (isset($params['validation-file'])) {
			$file = $params['validation-file'];
			if (! is_string($file) || empty($file)) {
				$err  = "validation file is the relative path to the ";
				$err .= "file holding validation mapping and must be a non ";
				$err .= "empty string";
				throw new DomainException($err);
			}
		}
		$finder = new FileFinder($file);
		$reader = new FileReader($finder);
		
		$map = $reader->import();
		if (isset($map['validators']) && is_array($map['validators'])) {
			ValidationManager::setValidatorMap($map['validators']);
		}

		if (isset($map['filters']) && is_array($map['filters'])) {
			ValidationManager::setFilterMap($map['filters']);
		}
		
    }
}
