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

use InvalidArgumentException;

/**
 * Wraps the openlog, syslog, and closelog calls used when logging an appfuel
 * or application specific message
 */
class Logger implements LoggerInterface
{
	/**
	 * @var string
	 */
	protected $adapter = null;

	/**
	 * @param	string	$indent
	 * @param	int		$opt
	 * @param	int		$facility
	 * @return	SysLogAdapter
	 */
	public function __construct(LogAdapterInterface $adapter = null)
	{
		if (null === $adapter) {
			$identity = 'appfuel';
			if (defined('AF_APP_KEY')) {
				$identity = AF_APP_KEY;
			}
			$adapter = new SysLogAdapter($identity);
		}
		$this->setAdapter($adapter);
	}

	/**
	 * @return	string
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * @param	LogAdapterInterface $adapter
	 * @return	null
	 */
	public function setAdapter(LogAdapterInterface $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * @param	LogEntryInterface $entry
	 * @return	bool
	 */
	public function logEntry(LogEntryInterface $entry)
	{
		$adapter = $this->getAdapter();
		if (! $adapter->openLog()) {
			return false;
		}
		
		$result = $adapter->writeEntry($entry);
		
		$adapter->closeLog();
		return $result;
	}

	/**
	 * @param	string	$text
	 * @param	int		$priority
	 * @return	bool
	 */
	public function log($text, $priority = LOG_INFO)
	{

		if (! empty($text) && is_string($text)) {
			$entry = new LogEntry($text, new LogPriority($priority));
		}
		else if ($text instanceof LogEntryInterface) {
			$entry = $text;
		}
		else {
			$err  = "first param must be a string or an object that ";
			$err .= "implments Appfuel\Log\LogEntryInterface";
			throw new InvalidArgumentException($err);
		}
		
		return $this->logEntry($entry);
	}
}
