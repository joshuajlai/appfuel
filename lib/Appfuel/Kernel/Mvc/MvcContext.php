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

use InvalidArgumentException,
	Appfuel\View\ViewTemplateInterface,
	Appfuel\DataStructure\Dictionary,
	Appfuel\DomainStructure\DictionaryInterface;

/**
 * The app context holds the input (get, post, argv etc..), handles errors and 
 * is a dictionary that can hold hold key value pairs allowing custom objects
 * specific to the application to be added without having to extends the 
 * context. The context is passed into each intercepting filter and then into
 * the action controllers process method.
 */
class MvcContext extends Dictionary implements MvcContextInterface
{
	/**
	 * The strategy used in this context. The mvc action be working with
	 * console, ajax or html
	 * @var string
	 */
	protected $strategy = null;

	/**
	 * The route associated to the this context
	 * @var string
	 */
	protected $route = null;

	/**
	 * Holds most of the user input given to the application. Used by the
	 * Front controller and all action controllers
	 * @var	AppInputInterface
	 */
	protected $input = null;

	/**
	 * List of acl roles for this context. The dispatcher asks the mvc action
	 * if this context will be allowed for processing based on these codes.
	 * @var	array
	 */
	protected $aclCodes = array();

	/**
	 * The mvc actions make assignments into the the view template which
	 * will be converted into a string for the output engine.
	 * @ViewTemplateInterface
	 */
	protected $view = null;

	/**
	 * The exit code is used by the framework to provide an exit status code
	 * @var int
	 */
	protected $exitCode = 200;

	/**
	 * @param	string	$strategy	console|ajax|html
	 * @param	AppInputInterface	$input
	 * @return	AppContext
	 */
	public function __construct($route, $strategy, AppInputInterface $input)
	{
		$this->setRoute($route);
		$this->setStrategy($strategy);
		$this->setInput($input);
	}

	/**
	 * @return	string
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * @return	string
	 */
	public function getStrategy()
	{
		return $this->strategy;
	}

	/**
	 * @return	ViewTemplateInterface
	 */
	public function getView()
	{
		return $this->view;
	}

	/**
	 * @param	ViewTemplateInterface $template
	 * @return	AppContext
	 */
	public function setView(ViewTemplateInterface $template)
	{
		$this->view = $template;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function buildView()
	{
		$view = $this->getView();
		if (! ($view instanceof ViewTemplateInterface)) {
			return '';
		}

		return $view->build();
	}

	/**
	 * @return	array
	 */
	public function getAclCodes()
	{
		return $this->aclCodes;
	}

	/**
	 * @param	string	$code
	 * @return	AppContext
	 */
	public function addAclCode($code)
	{
		if (empty($code) || ! is_string($code)) {
			throw new InvalidArgumentException(
				'role code must be a non empty string'
			);
		}
	
		if ($this->isAclCode($code)) {
			return $this;	
		}

		$this->aclCodes[] = $code;
		return $this;
	}

	/**
	 * @param	string	$code
	 * @return	bool
	 */
	public function isAclCode($code)
	{
		if (empty($code) || 
			! is_string($code) || ! in_array($code, $this->aclCodes, true)) {
			return false;
		}

		return true;
	}

	/**
	 * @return	int
	 */
	public function getExitCode()
	{
		return $this->exitCode;
	}
	
	/**
	 * @param	int	$code
	 * @return	AppContext
	 */
	public function setExitCode($code)
	{
		if (! is_int($code)) {
			throw new InvalidArgumentException('exit code must be an integer');
		}
		$this->exitCode = $code;
		return $this;
	}

	/**
	 * @return	ContextInputInterface
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * @param	AppInputInterface	$input
	 * @return	null
	 */
	protected function setInput(AppInputInterface $input)
	{
		$this->input = $input;
	}

	/**
	 * @param	string	$strategy
	 * @return	null
	 */
	protected function setStrategy($strategy)
	{
        if (empty($strategy) || ! is_string($strategy)) {
            $err = 'strategy must be a non empty string';
            throw new InvalidArgumentException($err);
        }
		$this->strategy = $strategy;
	}

	/**
	 * @param	string	$strategy
	 * @return	null
	 */
	protected function setRoute($route)
	{
		if (! is_string($route)) {
			$err = 'the route for this context must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->route = $route;
	}
}
