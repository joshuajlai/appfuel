<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel;

use Exception,
	Appfuel\Log\Logger,
	Appfuel\Log\LoggerInterface,
	Appfuel\Http\HttpOutput,
	Appfuel\Http\HttpResponse;

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
	 * @param	LoggerInterface			$logger
	 * @param	OutputEngineInterface	$engine
	 * @return	FaultHandler
	 */
	public function __construct(LoggerInterface  $logger = null)
	{
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
	 * @param	Exception	$e
	 * @return	null
	 */
	public function handleException(Exception $e = null)
	{
		$logger  = $this->getLogger();
		$msg  = $e->getMessage();
		$file = $e->getFile();
		$line = $e->getLine();
		$code = $e->getCode();
		$text = "uncaught exception $msg in $file:$line";

		$logger->log($text, LOG_ERR);
		
		if (empty($code)|| ! is_int($code)) {
			$code = 500;
		}

        $this->renderError($text, $code);
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
		$code   = 500;
		$logger = $this->getLogger();
		$text   = "$level: $msg in $file:$line";
		if (! (error_reporting() & $level)) {
			return false;
		}

		$logger->log($text, LOG_ERR);		
		$this->renderError($text, $code);
		exit($code);
	}

	/**
	 * @return	null
	 */
	public function renderError($text, $code)
	{
        if (PHP_SAPI === 'cli') {
			fwrite(STDERR, (string)$text . PHP_EOL);
			return;
        }
		
        $this->sendHttpOutput($text, $code);
	}

	/**
	 * @param	string	$text
	 * @param	int		$code
	 * @return	null
	 */
	protected function sendHttpOutput($text, $code = 500)
	{
		$output = new HttpOutput();
		$output->render(new HttpResponse($text, $code));
	}
}
