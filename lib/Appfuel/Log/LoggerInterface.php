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
namespace Appfuel\Log;

/**
 * The log adapter is used to by the logger to actually log the messages.
 */
interface LoggerInterface
{
	/**
	 * @return	LogAdapterInterface
	 */
	public function getAdapter();

	public function setAdapter(LogAdapterInterface $adapter);

	public function logEntry(LogEntryInterface $entry);

	/**
	 * @param	string	$text
	 * @param	int		$priority
	 * @return	bool
	 */
	public function log($text, $priority = LOG_INFO);
}
