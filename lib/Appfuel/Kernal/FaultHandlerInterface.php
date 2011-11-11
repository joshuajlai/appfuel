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
 * The fault handler is responsible for handling uncaught exceptions and
 * php errors.
 */
interface FaultHandlerInterface
{
	/**
	 * Used to log the error and exceptions
	 * @return	LoggerInterface
	 */
	public function getLogger();
}
