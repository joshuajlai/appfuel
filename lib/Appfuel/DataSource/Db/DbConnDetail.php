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
namespace Appfuel\DataSource\Db;

use InvalidArgumentException;

/**
 * Value object used to hold connection details to a database server. The 
 * array keys are fixed as:
 *
 * name: name of the database to connect to
 * host: database server host or ip
 * user: name of the user connecting
 * pass: password of the user connecting
 * opt:  array of options specific to the database vendor
 * 
 */
class DbConnDetail implements DbConnDetailInterface
{
	/**
	 * Can be either a hostname or an IP address
	 * @var string
	 */
	protected $host = null;

	/**
	 * User name connecting to the database server
	 * @var string
	 */
	protected $user = null;

	/**
	 * Password used to connect to the database server
	 * @var string
	 */
	protected $pass = null;

	/**
	 * Name of the database the user is connecting for
	 * @var string
	 */
	protected $db = null;

	/**
	 * List of vendor specific options used in the connection
	 * @var string
	 */
	protected $opts = array();

	/**
	 * @param	array	$data
	 * @return	DbParams
	 */
	public function __construct(array $data)
	{
		if (! isset($data['host'])) {
			$err = 'host is missing -(host)';
			throw new InvalidArgumentException($err);
		}
		$this->setHost($data['host']);

		if (! isset($data['user'])) {
			$err = 'user name is missing -(user)';
			throw new InvalidArgumentException($err);
		}
		$this->setUserName($data['user']);
	
		if (! isset($data['pass'])) {
			$err = 'password is missing -(pass)';
			throw new InvalidArgumentException($err);
		}
		$this->setPassword($data['pass']);
	
		if (! isset($data['name'])) {
			$err = 'database name is missing -(name)';
			throw new InvalidArgumentException($err);
		}
		$this->setDbName($data['name']);
			
		/* optional members */
		if (isset($data['options'])) {
			$this->setOptions($data['options']);
		}
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
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->pass;
	}

	/**
	 * @return string
	 */
	public function getDbName()
	{
		return $this->db;
	}

	/**
	 * @return string
	 */
	public function getOptions()
	{
		return $this->opts;
	}

	/**
	 * @param	string	$key
	 * @param	mixed	$default
	 * @return	mixed
	 */
	public function getOption($key, $default = null)
	{
		if (! is_string($key) || ! array_key_exists($key, $this->opts)) {
			return $default;
		}

		return $this->opts[$key];
	}

	/**
	 * @param	string	$host
	 * @return	null
	 */
	protected function setHost($host)
	{
		if (! is_string($host) || empty($host)) {
			$err = 'host must be a none empty string';
			throw new InvalidArgumentException($err);
		}

		$this->host = $host;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setUserName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'user name must be a none empty string';
			throw new InvalidArgumentException($err);
		}

		$this->user = $name;
	}

	/**
	 * @param	string	$pass
	 * @return	null
	 */
	protected function setPassword($pass)
	{
		if (! is_string($pass) || empty($pass)) {
			$err = 'password must be a none empty string';
			throw new InvalidArgumentException($err);
		}

		$this->pass = $pass;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setDbName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'password must be a none empty string';
			throw new InvalidArgumentException($err);
		}

		$this->db = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setOptions(array $options)
	{
		$this->opts = $options;
	}
}
