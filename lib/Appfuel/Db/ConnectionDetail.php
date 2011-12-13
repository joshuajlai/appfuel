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
namespace Appfuel\Db;

use InvalidArgumentException;

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
	protected $port = 3306;

	/**
	 * The socket or named pipe that should be used
	 * @var string
	 */
	protected $socket = null;

	/**
	 * @param	string	$vendor
	 * @param	string	$adapter
	 * @return	Dsn
	 */
	public function __construct(array $data)
	{
		$err = 'Connection detail failed: ';
		if (! isset($data['host']) || ! $this->isValidString($data['host'])) {
			$err .= 'host must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		$this->host = $data['host'];

		if (! isset($data['user']) || ! $this->isValidString($data['user'])) {
			$err .= 'username must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		$this->userName = $data['user'];
	
		if (! isset($data['pass']) || ! $this->isValidString($data['pass'])) {
			$err .= 'password must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		$this->password = $data['pass'];
	
		if (! isset($data['name']) || ! $this->isValidString($data['name'])) {
			$err .= 'database name must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		$this->dbName = $data['name'];
			
		/* optional members */
		if (isset($data['port']) && 
			is_int($data['port']) && $data['port'] > 0) {
			$this->port = $data['port'];
		}
	
		if (isset($data['socket']) && 
			$this->isValidString($data['socket'])) {
			$this->socket = $data['socket'];
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
	 * @return bool
	 */
	protected function isValidString($str)
	{
		return is_string($str) && ! empty($str);
	}
}
