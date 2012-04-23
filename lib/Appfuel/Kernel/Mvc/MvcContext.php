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
	Appfuel\Html\HtmlPageInterface,
	Appfuel\View\ViewDataInterface,
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
	 * Used to hold a string or object that implements __toString
	 * @var mixed
	 */
	protected $view = '';

	/**
	 * View format as determined by encoded route information. ex) route.json
	 * @var string
	 */
	protected $format = null;

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
	public function __construct($key, AppInputInterface $input)
	{
		$this->setRouteKey($key);
		$this->setInput($input);
	}

	/**
	 * @return	string
	 */
	public function getRouteKey()
	{
		return $this->routeKey;
	}

	/**
	 * @return	string
	 */
	public function getViewFormat()
	{
		return $this->format;
	}

	/**
	 * @param	string	$format
	 * @return	MvcContext
	 */
	public function setViewFormat($format)
	{
		if (! is_string($format)) {
			$err = 'view format must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->format = $format;
		return $this;
	}

	/**
	 * @return	
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
			throw new InvalidArgumentException($err);
		}

		$this->routeKey = $key;
	}
}
