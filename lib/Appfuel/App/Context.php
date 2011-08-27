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

use Appfuel\Framework\Exception,
	Appfuel\Framework\App\ContextInterface,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\App\Route\RouteInterface,
	Appfuel\Framework\App\Request\RequestInterface,
	Appfuel\Framework\Domain\Operation\OperationInterface;

/**
 * Message is a specialized disctionary used to pass throught the dispatch
 * system and into the action controllers. It allows the framework to inject
 * all the necessary objects into the action controllers and lets the 
 * controller pass back the document and any other meta data 
 */
class Context extends Dictionary implements ContextInterface
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
	 * Holds errors handled by any of the subsystems the context travels 
	 * through
	 * @var ErrorInterface
	 */
	protected $error = null;
	
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
	 * @return	bool
	 */
	public function isError()
	{
		return $this->error instanceof Exception;
	}

	/**
	 * We type on any exception not just the framework exception
	 *
	 * @param	string		$text
	 * @param	string		$code
	 * @param	Exception	$prev
	 * @return	Message
	 */
	public function setError($text, $code = 0, \Exception $prev = null)
	{
		$this->error = new Exception($text, $code, $prev);
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
		$this->error = null;
		return $this;
	}
}
