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
	Appfuel\DataStructure\Dictionary;

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
	 * Actual route key used in user request
	 * @var string
	 */
	protected $routeKey = null;

	/**
	 * The route value object associated with this context
	 * @var MvcRouteDetailInterface
	 */
	protected $routeDetail = null;

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
	 * @var mixed
	 */
	protected $view = '';

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
	public function __construct($routeKey, 
								MvcRouteDetailInterface $routeDetail,
								AppInputInterface $input,
								$view = null)
	{
		$this->setRouteKey($routeKey);
		$this->setRouteDetail($routeDetail);
		$this->setInput($input);

		if (null !== $view) {
			$this->setView($view);
		}
	}

	/**
	 * @return	string
	 */
	public function getRouteKey()
	{
		return $this->routeKey;
	}

	/**
	 * @return	RouteDetailInterface
	 */
	public function getRouteDetail()
	{
		return $this->routeDetail;
	}

	/**
	 * @return	string
	 */
	public function getNamespace()
	{
		return $this->getRouteDetail()
					->getNamespace();
	}

	public function getActionClass()
	{
		return $this->getRouteDetail()
					->getActionClass();
	}

	/**
	 * @return	bool
	 */
	public function isSkipPreFilters()
	{
		return $this->getRouteDetail()
					->isSkipPreFilters();
	}

	/**
	 * @return	string
	 */
	public function getViewStrategy()
	{
		return $this->getRouteDetail()
					->getViewDetail()
					->getStrategy();
	}

	/**
	 * @return	ViewTemplateInterface
	 */
	public function getView()
	{
		return $this->view;
	}

	/**
	 * @param	mixed	$view
	 * @return	bool
	 */
	public function isValidView($view)
	{
        if (is_scalar($view) ||
            (is_object($view) && is_callable(array($view, '__toString')))) {
			return true;
		}
	
		return false;
	}

	/**
	 * @return	bool
	 */
	public function isContextView()
	{
		return $this->isValidView($this->view);
	}

	/**
	 * @param	ViewTemplateInterface $template
	 * @return	AppContext
	 */
	public function setView($view)
	{
        if (! $this->isValidView($view)) {
            $err  = 'view must be a scalar value or an object that ';
            $err .= 'implements __toString';
            throw new InvalidArgumentException($err);
        }

		$this->view = $view;
		return $this;
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
	 * @return	bool
	 */
	public function isPublicAccess()
	{
		return $this->getRouteDetail()
					->isPublicAccess();
	}

	/**
	 * @return	bool
	 */
	public function isInternalOnlyAccess()
	{
		return $this->getRouteDetail()
					->isInternalOnlyAccess();
	}

	/**
	 * @return	bool
	 */
	public function isAccessAllowed()
	{
		$detail = $this->getRouteDetail();
		return $detail->isAccessAllowed($this->getAclCodes());
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
	 * @param	string	$key
	 * @return	null
	 */
	protected function setRouteKey($key)
	{
		if (! is_string($key)) {
			$err = 'route key must be a string';
			throw new InvalidArgumentException($key);
		}

		$this->routeKey = $key;
	}

	/**
	 * @param	string	$strategy
	 * @return	null
	 */
	protected function setRouteDetail(MvcRouteDetailInterface $detail)
	{
		$this->routeDetail = $detail;
	}
}
