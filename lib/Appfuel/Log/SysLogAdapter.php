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

use RunTimeException,
	InvalidArgumentException;

/**
 * Wraps the openlog, syslog, and closelog calls used when logging an appfuel
 * or application specific message
 */
class SysLogAdapter implements LogAdapterInterface
{
	/**
	 * String used in the openlog to tag each message
	 * @var string
	 */
	protected $identity = null;

	/**
	 * Used to indicate what logging options will be used when generating a
	 * log message:
	 * 
	 * LOG_CONS		if there is an error while sending data to the system 
	 *				logger, write directly to the system console
	 * LOG_NDELAY	open the connection to the logger immediately
	 * LOG_ODELAY	(default) delay opening the connection until the first 
	 *				message is logged
	 * LOG_PERROR	print log message to the standard error
	 * LOG_PID		include PID with each message
	 * 
	 * One or more of these options can be used with a bitwise OR 
	 */
	protected $options = null;

	/**
	 * Used to specify what type of program is logging the message. This
	 * allows you to specify (in your machine syslog configuration) how 
	 * messages coming from different facilities will be handled
	 * 
	 * LOG_AUTH		security/authorization messages (use LOG_AUTHPRIV instead
	 *				in systems where that constant is defined)
	 * LOG_AUTHPRIV	security/authorization messages (private)
	 * LOG_CRON		clock daemon (cron and at)
	 * LOG_DAEMON	other system daemons
	 * LOG_KERN		kenal messages
	 * LOG_LOCAL0.. reserved for local use
	 * LOG_LOCAL7
	 * LOG_LPR		line printer subsystem
	 * LOG_MAIL		mail subsystem
	 * LOG_NEWS		USENET news subsystem
	 * LOG_SYSLOG	messages generated internally by syslogd
	 * LOG_USER		generic user-level messages
	 * LOG_UUCP		UUCP subsystem
	 */
	protected $facility = null;
	
	/**
	 * @param	string	$indent
	 * @param	int		$opt
	 * @param	int		$facility
	 * @return	SysLogAdapter
	 */
	public function __construct($ident = 'appfuel', $opt = null, $facil = null)
	{

		$this->setIdentity($ident);
		
		
		if (null === $opt) {
			$opt = LOG_CONS | LOG_NDELAY | LOG_PID;
		}
		$this->setOptions($opt);
		
		if (null === $facil) {
			$facil = LOG_USER;
		}
		$this->setFacility($facil);
	}

	/**
	 * @return	string
	 */
	public function getIdentity()
	{
		return $this->identity;
	}

	/**
	 * @return	int
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @return	int
	 */
	public function getFacility()
	{
		return $this->facility;
	}

	/**
	 * @param	string	$text
	 * @param	int		$priority
	 * @return	bool
	 */
	public function write($text, $priority = LOG_INFO)
	{
		if (empty($text) || !is_string($text) || !($text = trim($text))) {
			throw new InvalidArgumentException("message must be a string");
		}

		if (! is_int($priority)) {
			$priority = LOG_INFO;
		}

		return syslog($priority, $text);
	}

	/**
	 * @return	bool
	 */
	public function openLog()
	{
		return openlog(
			$this->getIdentity(), 
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
	 * @throws	InvalidArgumentException
	 * @param	string	$code
	 * @return	null
	 */
	protected function setIdentity($key)
	{
		if (empty($key) || !is_string($key) || ! ($key = trim($key))) {
			$err = 'identity must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->identity = $key;
	}

	/**	
	 * @throws	InvalidArgumentException
	 * @param	int	$code
	 * @return	null
	 */
	protected function setOptions($code)
	{
		/* list of all valid combinations */
		$valid = array(
			LOG_CONS, 
			LOG_NDELAY,
			LOG_ODELAY,
			LOG_PERROR,
			LOG_PID,
			LOG_CONS|LOG_NDELAY,
			LOG_CONS|LOG_ODELAY,
			LOG_CONS|LOG_PERROR,
			LOG_CONS|LOG_PID,
			LOG_CONS|LOG_NDELAY|LOG_PERROR,
			LOG_CONS|LOG_NDELAY|LOG_PID,
			LOG_CONS|LOG_NDELAY|LOG_PERROR|LOG_PID,
			LOG_CONS|LOG_ODELAY|LOG_PERROR|LOG_PID,
			LOG_CONS|LOG_ODELAY|LOG_PERROR,
			LOG_CONS|LOG_ODELAY|LOG_PID,
			LOG_CONS|LOG_PID|LOG_PERROR,
			LOG_NDELAY|LOG_PERROR,
			LOG_NDELAY|LOG_PID,
			LOG_ODELAY|LOG_PERROR,
			LOG_ODELAY|LOG_PID,
			LOG_PID|LOG_PERROR,	
		);
		if (! is_int($code) || ! in_array($code, $valid, true)) {
			throw new InvalidArgumentException("option must be an integer");
		}

		$this->options = $code;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	int	$code
	 * @return	null
	 */
	protected function setFacility($code)
	{
		$valid = array(LOG_AUTH,LOG_AUTHPRIV,LOG_CRON,LOG_DAEMON,LOG_KERN,
					   LOG_LOCAL0, LOG_LOCAL1, LOG_LOCAL2, LOG_LOCAL3,
					   LOG_LOCAL4, LOG_LOCAL5, LOG_LOCAL6, LOG_LOCAL7,
					   LOG_LPR, LOG_MAIL, LOG_NEWS, LOG_SYSLOG, LOG_USER,
					   LOG_UUCP);

		if (! is_int($code) || ! in_array($code, $valid)) {
			$err = "facility must be one of the php defined LOG_* constants ";
			$err = "that define the syslog facility";
			throw new InvalidArgumentException($err);
		}

		$this->facility = $code;
	}
}
