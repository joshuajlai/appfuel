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
	Appfuel\Framework\App\Route\RouteInterface,
	Appfuel\Framework\App\Request\RequestInterface,
	Appfuel\Framework\App\Context\ContextInterface,
	Appfuel\Framework\Domain\User\UserInterface,
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
	 * @var	UserInterface
	 */
	protected $currentUser = null;

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
	public function __construct(RequestInterface $request,
								OperationInterface $op)
	{
		$this->request  = $request;
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
