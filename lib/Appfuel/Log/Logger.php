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
	public function __construct(LogAdapterInterface $adapter)
	{
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
	public function getSetAdapter(LogAdapterInterface $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * @param	LogEntryInterface $entry
	 * @return	bool
	 */
	public function logEntry(LogEntryInterface $entry)
	{
		return $this->log($entry->getText(), $entry->getPriority());
	}

	/**
	 * @param	string	$text
	 * @param	int		$priority
	 * @return	bool
	 */
	public function log($text, $priority = LOG_INFO)
	{
		$adapter = $this->getAdapter();
		if (! $adapter->open()) {
			return false;
		}

		if (! is_int($priority)) {
			$priority = LOG_INFO;
		}

		$result = $adapter->write($priority, $text);
		$adapter->close();
		return $result;
	}

	/**
	 * @return	bool
	 */
	public function openLog()
	{
		return openlog(
			$this->getIndentity(), 
			$this->getOptions(),
			$this->getFacility()
		); 
	}

	/**
	 * @return	bool
	 */
	public function closeLog()
	{
		return closelog();
	}

	/**	
	 * @param	string	$code
	 * @return	null
	 */
	protected function setIndentity($key)
	{
		if (empty($key) || !is_string($key) || ! ($key = trim($key))) {
			throw new RuntimeException("identity must be a non empty string");
		}

		$this->identity = $key;
	}

	/**	
	 * @param	int	$code
	 * @return	null
	 */
	protected function setOptions($code)
	{
		if (! is_int($code)) {
			throw new Exception("option must be an integer");
		}

		$this->options = $code;
	}

	/**
	 * @throws	RuntimeException
	 * @param	int	$code
	 * @return	null
	 */
	protected function setFacility($code)
	{
		$valid = array(LOG_AUTH,LOG_AUTHPRIV,LOG_CRON,LOG_DAEMON,LOG_KERN,
					   LOG_LOCAL0, LOG_LOCAL1, LOG_LOCAL2, LOG_LOCAL3,
					   LOG_LOCAL4, LOG_LOCAL5, LOG_LOCAL6, LOG_LOCAL7);

		if (! is_int($code) || ! in_array($code, $valid)) {
			throw new RunTimeException("not a valid facility given");
		}

		$this->facility = $code;
	}
}
