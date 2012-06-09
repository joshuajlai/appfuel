<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
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
			ValidationFactory::setValidatorMap($map['validators']);
		}

		if (isset($map['filters']) && is_array($map['filters'])) {
			ValidationFactory::setFilterMap($map['filters']);
		}

		if (isset($map['coordinator'])) {
			ValidationFactory::setCoordinatorClass($map['coordinator']);
		}
    }
}
