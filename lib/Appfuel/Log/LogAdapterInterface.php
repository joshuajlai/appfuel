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

use RunTimeException;

/**
 * The log adapter is used to by the logger to actually log the messages.
 */
interface LogAdapterInterface
{
	/**
	 * @param	LogEntryInterface	$entry
	 * @return	bool
	 */
	public function writeEntry(LogEntryInterface $entry);

	/**
	 * @param	string	$text
	 * @param	int		$priority
	 * @return	bool
	 */
	public function write($text, $priority = LOG_INFO);

	/**
	 * @return	bool
	 */
	public function openLog();

	/**
	 * @return	bool
	 */
	public function closeLog();
}
