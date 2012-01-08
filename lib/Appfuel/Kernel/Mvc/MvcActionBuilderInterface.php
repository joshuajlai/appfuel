<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\KernelRegistry,
    Appfuel\ClassLoader\StandardAutoLoader,
    Appfuel\ClassLoader\AutoLoaderInterface;

/**
 */
interface MvcActionBuilderInterface
{
	/**
	 * @param	string	$name
	 * @return	null
	 */
	static public function setActionClassName($name);

	/**
	 * @return	string
	 */
	static public function getActionClassName();

	/**
	 * @return	AutoLoaderInterface
	 */
	public function getClassLoader();

	/**
	 * @param	AutoLoaderInterface $loader
	 * @return	MvcActionBuilder
	 */
	public function setClassLoader(AutoLoaderInterface $loader);
}
