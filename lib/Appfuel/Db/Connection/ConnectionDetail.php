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
namespace Appfuel\Db\Connection;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Connection\ConnectionDetailInterface;

/**
 * Holds information neccessary to determine the vendor, vendor's adaptor and
 * connection details.
 */
class ConnectionDetail implements ConnectionDetailInterface
{
	/**
	 * Can be either a hostname or an IP address
	 * @var string
	 */
	protected $host = null;

	/**
	 * Mysql user name
	 * @var string
	 */
	protected $userName = null;

	/**
	 * Mysql password for user name
	 * @var string
	 */
	protected $password = null;

	/**
	 * Mysql database name
	 * @var string
	 */
	protected $dbName = null;

	/**
	 * Port number used to connect to mysql
	 * @var int
	 */
	protected $port = null;

	/**
	 * The socket or named pipe that should be used
	 * @var string
	 */
	protected $socket = null;

	/**
	 * Used in replication to indicate master or slave connections
	 * @var string
	 */
	protected $type = null;
	
	/**
	 * @param	string	$vendor
	 * @param	string	$adapter
	 * @return	Dsn
	 */
	public function __construct(array $detail)
	{

		$err = 'Connection detail failed:';
		if (! isset($detail['hostname']) ||
			! $this->isValidString($detail['hostname'])) {
			throw new Exception("$err host must be a non empty string");
		}
		$this->host = $detail['hostname'];

		if (! isset($detail['username']) || 
			! $this->isValidString($detail['username'])) {
			throw new Exception("$err username must be a non empty string");
		}
		$this->userName = $detail['username'];
	
		if (! isset($detail['password']) || 
			! $this->isValidString($detail['password'])) {
			throw new Exception("$err password must be a non empty string");
		}
		$this->password = $detail['password'];
	
		if (! isset($detail['dbname']) || 
			! $this->isValidString($detail['dbname'])) {
			throw new Exception("$err dbname must be a non empty string");
		}
		$this->dbName = $detail['dbname'];
			
		/* optional members */
		if (isset($detail['port']) && is_numeric($detail['port']) && 
			$detail['port'] > 0) {
			$this->port = $detail['port'];
		}
	
		if (isset($detail['socket']) && 
			$this->isValidString($detail['socket'])) {
			$this->socket = $detail['socket'];
		}
			
		if (isset($detail['type']) && $this->isValidString($detail['type'])) {
			$type = strtolower($detail['type']);	
			if (! in_array($type, array('master', 'slave'))) {
				throw new Exception("$err type must me master|slave -($type)");
			}
	
			$this->type = $type;
		}	
	}

	/**
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getVendor()
	{
		return $this->vendor;
	}

	/**
	 * @return string
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * @return string
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * @return string
	 */
	public function getUserName()
	{
		return $this->userName;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @return string
	 */
	public function getDbName()
	{
		return $this->dbName;
	}

	/**
	 * @return string
	 */
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * @return string
	 */
	public function getSocket()
	{
		return $this->socket;
	}

	/**
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return bool
	 */
	protected function isValidString($str)
	{
		return is_string($str) && ! empty($str);
	}
}
