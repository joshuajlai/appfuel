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
namespace Appfuel\App\Context;

use Appfuel\Framework\Exception,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\Domain\User\UserInterface,
	Appfuel\Framework\App\Context\ContextInterface,
	Appfuel\Framework\App\Context\ContextUriInterface,
	Appfuel\Framework\App\Context\ContextInputInterface,
	Appfuel\Framework\Action\ControllerNamespaceInterface,
	Appfuel\Framework\Domain\Action\ActionDomainInterface,
	Appfuel\Framework\Domain\Operation\OperationalRouteInterface;

/**
 * Message is a specialized disctionary used to pass throught the dispatch
 * system and into the action controllers. It allows the framework to inject
 * all the necessary objects into the action controllers and lets the 
 * controller pass back the document and any other meta data 
 */
class AppContext extends Dictionary implements ContextInterface
{
	/**
	 * Route string used to find the operational route.
	 * @var	string
	 */
	protected $routeString = null;

	/**
	 * Parameter string used in the uri. This does not have any route info
	 * @var string
	 */
	protected $uriParamString = null;

	/**
	 * The original uri string.
	 * @var string
	 */
	protected $uriString = null;

	/**
	 * The action domain holds all the necessary info about the action 
	 * controller being executed. 
	 * @var ActionDomainInterface
	 */
	protected $action = null;

	/**
	 * The access policy is a general permission flag that allows public routes
	 * to by pass authentication
	 * @return	string
	 */
	protected $accessPolicy = null;

	/**
	 * List of pre filters to be create
	 * @var array
	 */
	protected $preFilters = array();

	/**
	 * List of post filters to be created
	 * @var array
	 */
	protected $postFilters = array();

	/**
	 * Holds most of the user input given to the application. Used by the
	 * Front controller and all action controllers
	 * @var	ContextInputInterface
	 */
	protected $input = null;

	/**
	 * Holds the user domain that is preforming the operation in this context
	 * @var	UserInterface
	 */
	protected $currentUser = null;

	/**
	 * Resulting output from the controller reponsible for executing the 
	 * operation.
	 * @var mixed
	 */	
	protected $output = null;

	/**
	 * Holds errors handled by any of the subsystems the context travels 
	 * through
	 * @var Appfuel\Framework\Exception
	 */
	protected $exception = null;

	/**
	 * @param	ContextUriInterface		$uri
	 * @param	ContextInputInterface	$input
	 * @param	OperationalRouteInterface $opRoute
	 * @return	Context
	 */
	public function __construct(ContextUriInterface $uri,
								OperationalRouteInterface $opRoute,
								ContextInputInterface $input)
	{
		$this->routeString	  = $uri->getRouteString();
		$this->uriParamString = $uri->getParamString();
		$this->uriString	  = $uri->getUriString();

		$action = $opRoute->getAction();
		if (! $action instanceof ActionDomainInterface) {
			$err  = 'The action domain which details the action controller ';
		    $err .= ' need must implement Appfuel\Framework\Domain\Action';
			$err .= '\ActionDomainInterface';
			throw new Exception($err);
		}
		$this->action = $opRoute->getAction();

		$policy = $opRoute->getAccessPolicy();
		if (empty($policy) || ! is_string($policy)) {
			throw new Exception("Access Policy must be a non empty string");
		}

		$this->accessPolicy  = $policy;
		$this->preFilters    = $opRoute->getPreFilters();
		$this->postFilters   = $opRoute->getPostFilters();

		$this->input = $input;
	}

	/**
	 * @return	ContextInputInterface
	 */
	public function getInput()
	{
		return $this->input;
	}

    /**
     * @return string
     */
    public function getOriginalUriString()
    {
        return $this->uriString;
    }

    /**
     * @return  string
     */
    public function getRouteString()
    {
        return $this->routeString;
    }

    /**
     * @return  string
     */
    public function getUriParamString()
    {
        return $this->uriParamString;
    }

	/**
	 * @return	Appfuel\Framework\Domain\Action\ActionDomainInterface
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @return	string	
	 */
	public function getAccessPolicy()
	{
		return $this->accessPolicy;
	}

	/**
	 * @return	string
	 */
	public function getDefaultFormat()
	{
		return $this->defaultFormat;
	}

	/**
	 * @return	string
	 */
	public function getRequestType()
	{
		return $this->requestType;
	}

	/**
	 * @return	array
	 */
	public function getPreFilters()
	{
		return $this->preFilters;
	}

	/**
	 * @return	array
	 */
	public function getPostFilters()
	{
		return $this->postFilters;
	}

	/**
	 * @return	UserInterface
	 */
	public function getCurrentUser()
	{
		return $this->currentUser;
	}

	/**
	 * We make no assumption of the user implementation. Instead the developer
	 * may override the isValidUser and isCurrentUser checks to suite their 
	 * needs. Note: when using your own context remember to register the 
	 * context with the operation
	 *
	 * @param	UserInterface	$user
	 * @return	null
	 */
	public function setCurrentUser($user)
	{
		$this->currentUser = $user;
	}

	/**
	 * @return	bool
	 */
	public function isCurrentUser()
	{
		return $this->currentUser instanceof UserInterface;
	}

	/**
	 * @return	mixed
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * @param	mixed	$data
	 * @return	null
	 */
	public function setOutput($data)
	{
		$this->output = $data;
	}

	/**
	 * @return	bool
	 */
	public function isException()
	{
		return $this->exception instanceof Exception;
	}

	/**
	 * We type on any exception not just the framework exception
	 *
	 * @param	string		$text
	 * @param	string		$code
	 * @param	Exception	$prev
	 * @return	Message
	 */
	public function setException($text, $code=0, \Exception $prev=null)
	{
		$this->exception = new Exception($text, $code, $prev);
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getException()
	{
		return $this->exception;
	}

	/**
	 * @return	Message
	 */
	public function clearException()
	{
		$this->exception = null;
		return $this;
	}

	/**
	 * Allows the deleveloper to define what interface is valid for their
	 * user implementation
	 *
	 * @param	mixed	$user
	 * @return	bool
	 */
	protected function  isValidUser($user)
	{
		if ($user instanceof UserInterface) {
			return true;
		}

		return false;
	}
}
