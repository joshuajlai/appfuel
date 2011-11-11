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
namespace Appfuel\Kernal;

use Appfuel\Log\Logger,
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
	 * @param	LoggerInterface $logger
	 */
	public function __construct(LoggerInterface $logger = null)
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
}
