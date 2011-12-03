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
	Appfuel\Log\LoggerInterface,
	Appfuel\Kernel\OutputInterface;

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
	protected $output = null;

	/**
	 * @param	LoggerInterface			$logger
	 * @param	OutputEngineInterface	$engine
	 * @return	FaultHandler
	 */
	public function __construct(OutputInterface  $output,
								LoggerInterface  $logger = null)
	{
		$this->output = $output;
		if (null === $logger) {
			$logger = new Logger();
		}
		$this->logger = $logger;
	}

	/**
	 * @return	LoggerInterface
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**	
	 * @return	OutputInterface
	 */
	public function getOutputEngine()
	{
		return $this->output;
	}
	
	/**
	 * @param	Exception	$e
	 * @return	null
	 */
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

			$text = "uncaught exception $msg in $file:$line";
		}
		$logger->log($text, LOG_ERR);
			
		if (strlen($code) < 1 || ! is_int($code)) {
			$code = 500;
		}

		$display->renderError($text, $code);
		exit($code);
	}

	/**
	 * @param	int	$level	
	 * @param	string	$msg
	 * @param	int	$file
	 * @param	int	$line
	 * @param	mixed	$context
	 * @return	null
	 */
	public function handleError($level, $msg, $file, $line, $context)
	{
		$code    = 500;
		$logger  = $this->getLogger();
		$display = $this->getOutputEngine();

		$text = "$level: $msg in $file:$line";
		$logger->log($text, LOG_ERR);
		
		if (! (error_reporting() & $level)) {
			return false;
		}

		$display->renderError($text, $code);
		exit($code);
	}
}