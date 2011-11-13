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

use Exception,
	RunTimeException,
	Appfuel\Log\Logger,
	Appfuel\Log\LoggerInterface;

/**
 * The fault handler uses the Appfuel Logger with a SysLogAdapter to 
 * log errors and uncaught exceptions for the framework
 */
class FaultHandler implements FaultHandlerInterface
{
	/**
	 * Used to log the errors and exceptions
	 * @var	AppfuelLogger
	 */
	protected $logger = null;

	/**
	 * Used to ouput the error message 
	 * @var	 OutputEngineInterface
	 */
	protected $ouputEngine = null;

	/**
	 * @param	LoggerInterface			$logger
	 * @param	OutputEngineInterface	$engine
	 * @return	FaultHandler
	 */
	public function __construct(LoggerInterface $logger = null,
								OutputEngineInterface $engine = null)
	{
		if (null === $logger) {
			$logger = new Logger();
		}
		$this->logger = $logger;

		if (null === $engine) {
			$engine = new OutputEngine();
		}

		$this->outputEngine = $engine;
	}

	/**
	 * @return	LoggerInterface
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**	
	 * @return	OutputEngineInterface
	 */
	public function getOuputEngine()
	{
		return $this->outputEngine;
	}

	public function handleException(Exception $e)
	{
		$logger  = $this->getLogger();
		$display = $this->getOutputEngine();
		if ($e instanceof AppfuelException) {
			$code = $e->getCode();
			$text =(string) $e;
		}
		else {
			$msg  = $e->getMessage();
			$file = $e->getFile();
			$line = $e->getLine();
			$code = $e->getCode();
			$tags = 'untagged';

			$text = "exception $msg in $file:$line:$tags";
		}
		$logger->log($text, LOG_ERR);
			
		if (strlen($code) < 1 || ! is_int($code)) {
			$code = 1;
		}

		$display->outputGeneralError($text, $code);
		exit($code);
	}

	public function handleError($level, $msg, $file, $line, $context)
	{
		if (0 === $level) {
			return false;
		}

		$code    = 1;
		$logger  = $this->getLogger();
		$display = $this->getOutputEngine();

		$text = "$level: $msg in $file:$line";
		$logger->log($text, LOG_ERR);
		
		$display->outputGeneralError($text, $code);
		exit($code);
	}
}
