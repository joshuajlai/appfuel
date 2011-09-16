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
namespace Appfuel\View\ViewManager;

use Appfuel\Framework\Exception,
	Appfuel\Framework\FileInterface,
	Appfuel\Framework\View\ViewInterface,
	Appfuel\Framework\View\ViewManagerInterface;

/**
 * Handles assignments to the view 
 */
class ViewManager implements ViewManagerInterface
{
	/**
	 * @var	ViewCompositeInterface
	 */
	protected $htmlLayout = null;

	/**
	 * View Response used to render out the the user
	 * @var	ViewInterface
	 */
	protected $view = null;

	/**
	 * @return	ViewTemplateInterface
	 */
	public function getView()
	{
		return $this->view;
	}

	/**
	 * @param	ViewTemplateInterface
	 * @return	ViewManager
	 */
	public function setView(ViewTemplateInterface $view)
	{
		$this->view = $view;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isHtml()
	{
		return $this->view instanceof HtmlDocTemplateInterface;
	}

	/**
	 * @return	bool
	 */
	public function isJson()
	{
		return $this->view instanceof JsonTemplateInterface;
	}

	/**
	 * @return	bool
	 */
	public function isConsole()
	{
		return $this->view instanceof ConsoleTemplateInterface;
	}

	/**
	 * @param	string	$location	
	 * @param	string	$name
	 * @param	mixed	$label
	 * @return	ViewManager	
	 */
	public function assignTo($location, $name, $value)
	{
		$view = $this->getView();
		if (! $view instanceof ViewCompositeInterface) {
			throw new Exception("Can only assign to a composite view");
		}

		if (empty($location) || ! is_string($location)) {
			throw new Exception("location must be a non empty string");
		}

		$parts = explode('.', $location);
		
		$template = $view;
		foreach ($parts as $key) {
			if (empty($key)) {
				continue;
			}
			if (! $view->templateExists($key)) {
				throw new Exception("could not find template -($key)");
			}
			$template = $template->getTemplate($key);
		}

		$template->assign($name, $value);
		return $this;
	}

	/**
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	ViewManager
	 */
	public function assign($name, $value)
	{
		$view = $this->getView();
		if (! $view instanceof ViewTemplateInterface) {
			throw new Exception("Can only assign to a ViewTemplateInterface");
		}
		$view->assign($name, $value);
	}
}
