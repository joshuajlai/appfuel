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

use Appfuel\View\ViewTemplateInterface,
	Appfuel\View\AjaxTemplateInterface;

/**
 */
interface MvcActionInterface
{
	/**
	 * @return	MvcActionDispatcherInterface
	 */
	public function getDispatcher();

	/**
	 * @param	MvcActionDispatcherInterface
	 * @return	null
	 */
	public function setDispatcher(MvcActionDispatcherInterface $dispatcher);

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
	public function processHtml(AppContextInterface $context,
								ViewTemplateInterface $view);
	
	/**
	 * @param	AppContextInterface		$context
	 * @param	ViewTemplateInterface	$view
	 * @return	mixed	null | AppContextInterface 
	 */
	public function processAjax(AppContextInterface $context,
								AjaxTemplateInterface $view);

	/**
	 * @param	AppContextInterface			  $context
	 * @param	ConsoleViewTemplateInterface  $view
	 * @return	mixed	null | AppContextInterface 
	 */
	public function processConsole(AppContextInterface $context,
								   ViewTemplateInterface $view);
}
