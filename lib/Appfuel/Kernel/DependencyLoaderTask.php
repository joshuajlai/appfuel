<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel;

use DomainException,
	Appfuel\ClassLoader\ManualClassLoader;

/**
 * Manual load dependent php classes into memory
 */
class DependencyLoaderTask extends StartupTask
{
	/**
	 * @return	PHPIniStartup
	 */
	public function __construct()
	{
		$this->setRegistryKeys(array(
			'depend-files'	=> array(),
			'depend-classes'=> array(),
			'depend-lib-classes' => array()
		));
	}

	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		if (empty($params)) {
			return;
		}
		
		$msg = '';
		if (isset($params['depend-files'])) {
			$list = $params['depend-files'];
			if (! is_array($list)) {
				$err  = "a dependency file list was declared but was not ";
				$err .= "an array";
				throw new DomainException($err);
			}

			foreach ($list as $data) {
				if (is_string($data)) {
					$file = $data;
					$isLibPath = true;
				}
				else if (is_array($data)) {
					$file = current($data);
					$isLibPath = next($data);
				}
				else {
					$err  = "a dependency can be a string, which when used ";
					$err .= "assumes AF_LIB_PATH will be prepended to each ";
					$err .= "path, or an array where the first item is the ";
					$err .= "path to file holding the dependency map and the ";
					$err .= "second item is flag used to determine if ";
					$err .= "AF_LIB_PATH will be used";
					throw new DomainException($err);
				}

                $full = AF_BASE_PATH . DIRECTORY_SEPARATOR . $file;
				ManualClassLoader::loadCollectionFromFile($full, $isLibPath);
			}

			$nbr = count($list);
			$msg = "$nbr files were proccessed as dependency lists: ";
		}

		if (isset($params['depend-lib-classes'])) {
			$list = $params['depend-lib-classes'];
			if (! is_array($list)) {
				$err  = "list of dependency classes was declared but is not ";
				$err .= "an array";
				throw new DomainException($err);
			}

			foreach ($list as $className => $path) {
				ManualClassLoader::loadClass($className, $path);				
			}

			$nbr = count($list);
			$msg .= "$nbr lib classes were proccessed as dependencies: ";
		}

		if (isset($params['depend-classes'])) {
			$list = $params['depend-classes'];
			if (! is_array($list)) {
				$err  = "list of dependency classes was declared but is not ";
				$err .= "an array";
				throw new DomainException($err);
			}

			foreach ($list as $className => $path) {
				ManualClassLoader::loadClass($className, $path, false);				
			}


			$msg .= "$nbr classes were proccessed as dependencies: ";
		}

		$this->setStatus($msg);
	}
}
