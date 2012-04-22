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
namespace Appfuel\Kernel\Startup;

use DomainException,
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
		$this->setDataKeys(array('php-autoloader' => null));
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
		$data = $params['php-autoloader'];

		if (is_string($data)) {
			$loader = new $data();
			if (! $loader instanceof AutoLoaderInterface) {
				$err  = "loader -($data) must implement Appfuel\ClassLoader";
				$err .= "\AutoLoaderInterface";
				throw new DomainException($err);
			}

			$loader->addPath(AF_LIB_PATH);
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
