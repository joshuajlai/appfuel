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
namespace Appfuel\App;

use Appfuel\Data\Dictionary,
	Appfuel\Framework\Exception,
	Appfuel\Framework\App\MessageInterface,
	Appfuel\Framework\App\Route\RouteInterface,
	Appfuel\Framework\App\Request\RequestInterface;

/**
 * Message is a specialized disctionary used to pass throught the dispatch
 * system and into the action controllers. It allows the framework to inject
 * all the necessary objects into the action controllers and lets the 
 * controller pass back the document and any other meta data 
 */
class Message extends Dictionary implements MessageInterface
{
	/**
	 * Used by the front controller to build and configure an action controller
	 * @var	RouteInterface
	 */
	protected $route = null;

	/**
	 * Used by the front controller, action controller and possibly the 
	 * action builder to retrieve user input
	 *
	 * @var	RequestInterface
	 */
	protected $request = null;

	/**
	 * Determines how the data is returned. Can be specified by the user or
	 * the route. 
	 *
	 * @var string
	 */
	protected $responseType = null;

	/**
	 * Error message for an error that occured during the dispatch/execute cycle
	 * @var string
	 */
	protected $error = null;
	
	/**
	 * Flag used to determine if the message is in an error state
	 * @var bool
	 */
	protected $isError = false;

	/**
	 * @return	RouteInterface
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * @param	RouteInterface $route
	 * @return	Message
	 */
	public function setRoute(RouteInterface $route)
	{
		$this->route = $route;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isRoute()
	{
		return $this->route instanceof RouteInterface;
	}

	/**
	 * @return	RequestInterface
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @param	RouteInterface $route
	 * @return	Message
	 */
	public function setRequest(RequestInterface $request)
	{
		$this->request = $request;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isRequest()
	{
		return $this->request instanceof RequestInterface;
	}

	/**
	 * @return string
	 */
	public function getResponseType()
	{
		return $this->responseType;	
	}

	/**
	 * @param	string	$type
	 * @return	Message
	 */
	public function setResponseType($type)
	{
		if (! is_string($type) || empty($type)) {
			throw new Exception("response type must be a non empty string");
		}

		$this->responseType = $type;
		return $this;
	}

	/**
	 * Load the response type from the request or route. When there is no 
	 * reponse type from the request use the route's value. Returns the 
	 * responseType that was set
	 *
	 * @return string
	 */
	public function loadResponseType()
	{
		$route   = $this->getRoute();
		$request = $this->getRequest();
		if (! $route || ! $request) {
			throw new Exception(
				'Can not load response type without request or route'
			);
		}

		$responseType = $route->getResponseType();
		if ($request->isResponseType()) {
			$temp = $request->getResponseType();
			if (is_string($temp) && ! empty($temp)) {
				$responseType = $temp;
			}
		}
		$this->setResponseType($responseType);
		return $responseType;
	}

	/**
	 * @return	bool
	 */
	public function isError()
	{
		return $this->isError;
	}

	/**
	 * @param	string	$text
	 * @return	Message
	 */
	public function setError($text)
	{
		$this->error   = $text;
		$this->isError = true;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return	Message
	 */
	public function clearError()
	{
		$this->isError = false;
		$this->error   = null;
		
		return $this;
	}
}
