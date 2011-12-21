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
namespace Appfuel\Kernel\Mvc;

/**
 */
interface MvcActionInterface
{
	/**
	 * @return	string
	 */
	public function getRoute();

	/**
	 * @return	MvcActionDispatcherInterface
	 */
	public function getDispatcher();

	/**
	 * Used to determine acl controll
	 * 
	 * @param	array	$codes
	 */
	public function isContextAllowed(array $codes);

	/**
	 * @param	AppContextInterface		$context
	 * @param	ViewTemplateInterface	$view
	 * @return	mixed	null | AppContextInterface 
	 */
	public function process(MvcContextInterface $context);
}
