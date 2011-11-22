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

use Appfuel\Framework\View\JsonTemplateInterface,
	Appfuel\Framework\View\ViewTemplateInterface,
	Appfuel\Framework\Console\ConsoleViewTemplateInterface;

/**
 */
class MvcAction implements MvcActionInterface
{
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
	public function processJson(AppContextInterface $context,
								JsonTemplateInterface $view)
	{
		return $context;
	}

	/**
	 * @param	AppContextInterface $context
	 * @param	ConsoleViewTemplateInterface $view
	 * @return	AppContextInterface
	 */
	public function processConsole(AppContextInterface $context,
								   ConsoleViewTemplateInterface $view)
	{
		return $context;
	}
}
