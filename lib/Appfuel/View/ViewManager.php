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
	 * View Response used to render out the the user
	 * @var	ViewInterface
	 */
	protected $view = null;

	/**
	 * @return	ViewInterface
	 */
	public function getView()
	{
		return $this->view;
	}

	/**
	 * @param	ViewInterface	$view
	 * @return	ViewManager
	 */
	public function setView(ViewInterface $view)
	{
		$this->view = $view;
		return $this;
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
		if (! $view instanceof ViewInterface) {
			throw new Exception("can not assign to a view that is not set");
		}

		if ($view instanceof Html\Response) {
			$layout = $view->getLayout();
			$layout->assignTo($location, $name, $value);
		} else {
			$view->assign($name, $value);
		}

	}

	/**
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	ViewManager
	 */
	public function assign($name, $value)
	{
		if ($view instanceof Html\Response) {
			$layout = $view->getLayout();
			$layout->assignTo('body', $name, $value);
		} else {
			$view->assign($name, $value);
		}

		return $this;
	}
}
