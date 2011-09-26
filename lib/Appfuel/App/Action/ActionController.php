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
namespace Appfuel\App\Action;


use Appfuel\Framework\Exception,
    Appfuel\Framework\App\ContextInterface,
    Appfuel\Framework\View\DocumentInterface,
	Appfuel\Framework\View\ViewManagerInterface;

/**
 * The Parent class to all Appfuel Action controllers. 
 */
class ActionController implements ControllerInterface
{
	/**
	 * Input scheme handles validation and sanitization of all user inputs
	 * for the controller
	 * @var InputScheme
	 */
	protected $inputScheme = null;

	/**
	 * View Manager handles assignments to the document this controller is
	 * working on.
	 * @var string
	 */
	protected $viewManager = null;

	/**
	 * @return ViewManager
	 */
	public function getViewManager()
	{
		return $this->viewManager;
	}

	/**
	 * @param	ViewManager	$manager
	 * @return	Controller  
	 */
	public function setViewManager(ViewManagerInterface $manager)
	{
		$this->viewManager = $manager;
		return $this;
	}

	/**
	 * 
	 * @param	MessageInterface $msg
	 */
	public function initialize(ContextInterface $context)
	{
		return $context;
	}

	/**
	 * @param	MessageInterface $msg
	 */
	public function execute(ContextInterface $msg)
	{
		return $context;
	}
}
