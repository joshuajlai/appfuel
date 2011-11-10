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
 * Appfuel use the sys log priorities as its general priority object
 */
class LogPriority implements  LogPriorityInterface
{
	/**
	 * List of the valid priorities for the sys log
	 * @var array
	 */
	static protected $valid = array(
		LOG_EMERG,
		LOG_ALERT,
		LOG_CRIT,
		LOG_ERR,
		LOG_WARNING,
		LOG_NOTICE,
		LOG_INFO,
		LOG_DEBUG
	);

	/**
	 * @var int
	 */
	protected $level = null;

	/**
	 * @param	int	$level
	 * @return	SysLogPriority
	 */
	public function __construct($level = LOG_INFO)
	{
		if (! is_int($level) || ! in_array($level, self::$valid, true)) {
			$level = LOG_INFO;
		}

		$this->level = $level;
	}

	/**
	 * @return	scalar
	 */
	public function getLevel()
	{
		return $this->level;
	}

	/**
	 * @return	string
	 */
	public function __toString()
	{
		return (string)$this->getLevel();
	}
}
