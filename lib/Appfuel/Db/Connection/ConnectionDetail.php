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
	 * The type of database in use mysql, postgres, sqllite, etc ..
	 * @var string
	 */
	protected $vendor = null;

	/**
	 * Name of the vendor specific adapter the database handler will need
	 * @var string
	 */
	protected $adapter = null;

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
	public function __construct($vendor, $adapter)
	{
		$err = 'Dsn constructor failure:';
		if (! $this->isValidString($vendor)) {
			throw new Exception("$err user name must be a non empty string");
		}
		$this->vendor = $vendor;

		if (! $this->isValidString($adapter)) {
			throw new Exception("$err password must be a non empty string");
		}
		$this->adapter = $adapter;
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
	 * @throws	Exception
	 * @param	string	$host
	 * @return	Dsn
	 */
	public function setHost($host)
	{
		if (! $this->isValidString($host)) {
			throw new Exception('host must be a non empty string');
		}
		$this->host = $host;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUserName()
	{
		return $this->userName;
	}

	/**
	 * @throws	Exception
	 * @param	string	$name
	 * @return	Dsn
	 */
	public function setUserName($name)
	{
		if (! $this->isValidString($name)) {
			throw new Exception('user name must be a non empty string');
		}
		$this->userName = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @throws	Exception
	 * @param	string	$pass
	 * @return	Dsn
	 */
	public function setPassword($pass)
	{
		if (! is_scalar($pass) || empty($pass)) {
			throw new Exception('password must be a non empty scalar value');
		}
		$this->password = $pass;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDbName()
	{
		return $this->dbName;
	}

	/**
	 * @throws	Exception
	 * @param	string	$name
	 * @return	Dsn
	 */
	public function setDbName($name)
	{
		if (! $this->isValidString($name)) {
			throw new Exception('db name must be a non empty string');
		}
		$this->dbName = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * @throws	Exception
	 * @param	int		$nbr
	 * @return	Dsn
	 */
	public function setPort($nbr)
	{
		if (! is_numeric($nbr) || $nbr <= 0) {
			throw new Exception('port must be an int greater than 0');
		}
		$this->port = $nbr;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSocket()
	{
		return $this->socket;
	}

	/**
	 * @throws	Exception
	 * @param	string	$socket
	 * @return	Dsn
	 */
	public function setSocket($socket)
	{
		if (! $this->isValidString($socket)) {
			throw new Exception('socket name must be a non empty string');
		}
		$this->socket = $socket;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getType()
	{

		return $this->type;
	}

	/**
	 * @return	string
	 */
	public function setType($type)
	{
		$err = 'type must be (master|slave) only';
		if (empty($type) || ! is_string($type)) {
			throw new Exception($err);
		}

		$type = strtolower($type);
		if (! in_array($type, array('master', 'slave'))) {
			throw new Exception($err);
		}
	
		$this->type = $type;
		return $this;
	}

	/**
	 * @return bool
	 */
	protected function isValidString($str)
	{
		return is_string($str) && ! empty($str);
	}
}
