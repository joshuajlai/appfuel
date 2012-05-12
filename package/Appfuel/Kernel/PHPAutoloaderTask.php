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
namespace Appfuel\Kernel;

use DomainException,
	RunTimeException,
	Appfuel\ClassLoader\AutoLoaderInterface;

/**
 * register php autoloader
 */
class PHPAutoloaderTask extends StartupTask
{
	/**
	 * @return	PHPIniStartup
	 */
	public function __construct()
	{
		$this->setRegistryKeys(array('php-autoloader' => null));
	}

	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		if (empty($params) || ! isset($params['php-autoloader'])) {
			return;
		}

		if (! defined('AF_CODE_PATH')) {
			$err  = "the absolute path to the directory where all php ";
			$err .= "namespaces are found must be defined in a constant ";
			$err .= "named AF_CODE_PATH";
			throw new RunTimeException($err);
		}

		$data = $params['php-autoloader'];
		if (is_string($data)) {
			$loader = new $data();
			if (! $loader instanceof AutoLoaderInterface) {
				$err  = "loader -($data) must implement Appfuel\ClassLoader";
				$err .= "\AutoLoaderInterface";
				throw new DomainException($err);
			}

			$loader->addPath(AF_CODE_PATH);
			$loader->register();
			$this->setStatus("autoloader class  -($data) registered");
		}
		else if (is_array($data)) {
			$func = current($data);
			if (null === $func) {
				spl_autoload_register();
			}
			else {
				$isThrow   = (false === next($data))   ? false : true;
				$isPrepend = (true === next($data)) ? true  : false;
				spl_autoload_register($func, $isThrow, $isPrepend);
			}

			$this->setStatus("autoloader was manual registered");
		}
	}
}
