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

/**
 * Used to build action controllers
 */
interface MvcActionFactoryInterface
{
	/**
	 * @return	string
	 */
	public function getActionClass();

	/**
	 * @param	string	$className
	 * @return	ActionFactoryInterface
	 */
	public function setActionClass($className);

	/**
	 * @param	string	$namespace
	 * @return	ActionControllerInterface
	 */
	public function createMvcAction($namespace);
}
