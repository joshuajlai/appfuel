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
	Appfuel\Framework\Domain\Operation\OperationInterface;

/**
 * Message is a specialized disctionary used to pass throught the dispatch
 * system and into the action controllers. It allows the framework to inject
 * all the necessary objects into the action controllers and lets the 
 * controller pass back the document and any other meta data 
 */
class AppContext extends Dictionary implements ContextInterface
{
	/**
	 * An operation defines the action this context was created for. It used
	 * by the front controller for validation and execution
	 * @var	OperationInterface
	 */
	protected $operation = null;

	/**
	 * Holds most of the user input given to the application. Used by the
	 * Front controller and all action controllers
	 * @var	RequestInterface
	 */
	protected $request = null;

	/**
	 * Used to parse the route string and parameters from the http get or cli
	 * @var	UriInterface
	 */
	protected $uri = null;

	/**
	 * @var	UserInterface
	 */
	protected $currentUser = null;

	/**
	 * Data the controller wants the render engine to output.
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
	 * @param	RequestInterface	$request
	 * @return	Context
	 */
	public function __construct(ContextUriInterface $uri,
								ContextInputInterface $request,
								OperationInterface $op)
	{
		$this->uri       = $uri;
		$this->request   = $request;
		$this->operation = $op;
	}

	/**
	 * @return	OperationInterface
	 */
	public function getOperation()
	{
		return $this->operation;
	}

	/**
	 * @return	RequestInterface
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return	UriInterface
	 */
	public function getUri()
	{
		return $this->uri;
	}

    /**
     * @return string
     */
    public function getUriString()
    {
        return $this->getUri()
                    ->getUriString();
    }

    /**
     * @return  string
     */
    public function getRouteString()
    {
        return $this->getUri()
                    ->getPath();
    }

    /**
     * @return  string
     */
    public function getParamString()
    {
        return $this->getUri()
                    ->getParamString();
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
