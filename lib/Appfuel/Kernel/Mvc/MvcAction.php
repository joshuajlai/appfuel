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

use Appfuel\View\AjaxTemplateInterface,
	Appfuel\View\ViewTemplateInterface;

/**
 */
class MvcAction implements MvcActionInterface
{
	/**
	 * Used to make a call to other mvc actions
	 * @var MvcActionDispatcherInterface
	 */
	protected $dispatcher = null;

	/**
	 * @param	MvcActionDispatcherInterface $dispatcher
	 * @return	null
	 */
	public function setDispatcher(MvcActionDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}
	
	/**
	 * @param	MvcActionDispatcher
	 * @return	null
	 */
	public function getDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * @param	array	$codes
	 * @return	bool
	 */
	public function isContextAllowed(array $codes)
	{
		return false;
	}

	/**
	 * @param	AppContextInterface $context
	 * @param	ViewTemplateInterface $view
	 * @return	AppContextInterface
	 */
	public function processHtml(AppContextInterface $context,
								ViewTemplateInterface $view)
	{
		return $context;
	}

	/**
	 * @param	AppContextInterface $context
	 * @param	JsonTemplateInterface $view
	 * @return	AppContextInterface
	 */
	public function processAjax(AppContextInterface $context,
								AjaxTemplateInterface $view)
	{
		return $context;
	}

	/**
	 * @param	AppContextInterface $context
	 * @param	ConsoleViewTemplateInterface $view
	 * @return	AppContextInterface
	 */
	public function processConsole(AppContextInterface $context,
								   ViewTemplateInterface $view)
	{
		return $context;
	}
}
