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
namespace Appfuel\Kernel;

use Appfuel\Log\Logger,
	Appfuel\Log\LoggerInterface,
	Appfuel\Error\ErrorStack,
	Appfuel\Error\ErrorStackInterface,
	

/**
 * The Framework object extended by any class that needs general framework
 * functionality like logging, exceptions, 
 */
class FrameworkObject implements FrameworkObjectInterface
{
	/**
	 * @var	AppfuelErrorInterface
	 */
	protected $errorStack = null;

	/**
	 * @param	ErrorStackInterface
	 * @return	FrameworkObject
	 */	
	public function __construct(ErrorStackInterface $errorStack = null,
								LoggerInterface $logger = null)
	{
		if (null === $errorStack) {
			$errorStack = new ErrorStack();
		}
		$this->setErrorStack($errorStack);

		if (null === $logger) {
			$logger = new Logger();
		}
		$this->setLogger($logger);
	}

	/**
	 * @return	bool
	 */
	public function isError()
	{
		return $this->getErrorStack()
				    ->isError();
	}

	/**
	 * @param	AppfuelErrorInterface	$error
	 * @return	FrameworkObject
	 */
	public function setErrorStack(ErrorStackInterface $stack)
	{
		$this->errorStack = $stack;
		return $this;
	}

	/**
	 * @return	ErrorStackInterface
	 */
	public function getErrorStack()
	{
		return $this->errorStack;
	}

	/**
	 * @param	string	$text	
	 * @param	scalar	$code
	 * @return	FrameworkObject
	 */
	public function addError($msg, $code = null) 
	{
		$this->getErrorStack()
			 ->addError($msg, $code);

		return $this;
	}

	/**
	 * @return	ErrorItemInterface | null when no error exists
	 */
	public function getError()
	{
		return $this->getErrorStack()
					->current();
	}

	/**
	 * @return	LoggerInterface
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**
	 * @param	LoggerInterface $logger
	 * @return	FrameworkObject
	 */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
		return $this;
	}

	/**
	 * Log a message and priority level to the logger. In appfuel this defaults
	 * to the syslog
	 * 
	 * @param	string	$msg
	 * @param	int		$level
	 * @return	FrameworkObject
	 */
	public function log($msg, $level)
	{
		$this->getLogger()
			 ->log($msg, $level);

		return $this;
	}
}
