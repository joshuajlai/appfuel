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
namespace Appfuel\DataSource\Db\Mysql\Mysqli;

use RunTimeException,
	InvalidArgumentException,
	mysqli as MysqliDriver,
	Appfuel\DataStructure\Dictionary,
	Appfuel\DataStructure\DictionaryInterface;

/**
 * The primary responsibilty of the DbConnection is encapsulate vendor specific
 * details for connecting and disconnecting from the database server as well 
 * as creating a DbQuery object used to issue database queries. Opening and 
 * closing as well as finding errors are done throug delegation of the native
 * mysqi object which is created in the constructor
 */
class MysqliConn implements MysqliConnInterface
{
	/**
	 * Value object used to hold the connection details
	 * @var	DictionaryInterface
	 */
	protected $params = null;
	
	/**
	 * @var int
	 */
	protected $defaultPort = 3306;

	/**
	 * Mysqli object used to interact with the database
	 * @var	mysqli
	 */	
	protected $driver = null;

	/**
	 * Flag used to determine if a connection to the database has been 
	 * established. This connection is always done through mysqli_real_connect
	 * @var bool
	 */
	protected $isConnected = false;

	/**
	 * Connection error 
	 * @var array
	 */
	protected $error = array();

	/**
	 * @param	DbConnDetailInterface	$detail
	 * @return	MysqliConn
	 */
	public function __construct($conn)
	{
		if (is_array($conn)) {
			$conn = new Dictionary($conn);
		}
		else if (! ($conn instanceof DictionaryInterface)) {
			$err  = 'mysqli connection parameters must be either an array ';
			$err .= 'or an object that implements Appfuel\DataStructure';
			$err .= '\DictionaryInterface';
			throw new InvalidArgumentException($err); 
		}
		$this->params = $conn;
	}

	/**
	 * @return	ConnectionDetail
	 */
	public function getConnectionParams()
	{
		return $this->params;
	}

	/**
	 * @return	int
	 */
	public function getDefaultPort()
	{
		return $this->defaultPort;
	}

	/**
	 * @param	int	$nbr
	 * @return	MysqliConn
	 */
	public function setDefaultPort($nbr)
	{
		if (! is_int($nbr) || $nbr < 1) {
			$err = 'default port must be a int greater than 0';
			throw new InvalidArgumentException($err);
		}

		$this->defaultPort = $nbr;
		return $this;
	}

	/**
	 * @return	MysqliDriver
	 */
	public function createDriver()
	{
		return mysqli_init();
	}

	/**
	 * @param	MysqliDriver	$mysqli
	 * @return	MysqliConn
	 */
	public function setDriver($mysqli)
	{
		if (! $mysqli instanceof MysqliDriver) {
			$err = 'driver must be a mysqli object';
			throw new InvalidArgumentException($mysqli);
		}

		$this->driver = $mysqli;
		return $this;
	}

	/**
	 * @return	Mysqli
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * @return	bool
	 */
	public function isDriver()
	{
		return $this->driver instanceof MysqliDriver;
	}

	/**
	 * Connect to the database using the ConnectionDetail. I surpress errors 
	 * because php is only going to raise an exception which gives php's error 
	 * code and not mysql. We can provide better error handling by setting 
	 * the drivers error text and code.
	 *
	 * @return	bool 
	 */
	public function connect()
	{
		if ($this->isConnected()) {
			return true;
		}

		$driver = $this->getDriver();
		if (! ($driver instanceof MysqliDriver)) {
			$driver = $this->createDriver();
			if (! $driver) {
				$err = 'connect not started: could not initialize mysqli';
				throw new RunTimeException($err);
			}
			$this->setDriver($driver);
		}
		
		$params = $this->getConnectionParams();
		$flags  = $params->get('flags', null);
		if (is_array($flags)) {
			$flags = $this->buildConnectFlags($flags);
		}

		$options = $params->get('options', null);
		if (is_array($options)) {
			$this->setConnectionOptions($options);
		}

		$this->isConnected = @mysqli_real_connect(
			$driver,
			$params->get('host'),
			$params->get('user'),
			$params->get('pass'),
			$params->get('name'),
			$params->get('port', $this->getDefaultPort()),
			$params->get('socket', null)
		);
	
		if (! $this->isConnected) {
			$this->setError($driver->connect_errno, $driver->connect_error); 
			return false;
		}

		return true; 
	}

	/**
	 * @param	array $params
	 * @return	int
	 */
	public function buildConnectionFlags(array $data)
	{
		$result = 0;
		$flags = array();
		if (in_array('compress', $data)) {
			$flag[] = MYSQLI_CLIENT_COMPRESS;
		}

		if (is_array('found-rows', $data)) {
			$flags[] = MYSQLI_CLIENT_FOUND_ROWS;
		}

		if (is_array('ignore-space', $data)) {
			$flags[] = MYSQLI_CLIENT_IGNORE_SPACE;
		}

		if (is_array('interactive', $data)) {
			$flags[] = MYSQLI_CLIENT_INTERACTIVE;
		}

		if (is_array('ssl', $data)) {
			$flags[] = MYSQLI_CLIENT_SSL;
		}

		foreach ($flags as $flag) {
			$result |= $flag;
		}

		return $result;
	}

	/**
	 * @param	array $params
	 * @return	int
	 */
	public function setConnectionOptions(MysqliDriver $driver, array $data)
	{
		if (isset($data['connect-timeout'])) {
			$seconds = $data['connect-timeout'];
			if (! is_int($seconds) || $seconds < 0) {
				$err = 'MYSQLI_OPT_CONNECT_TIMEOUT must be a postive integer';
				throw new InvalidArgumentException($err);
			}
		
			$driver->options(MYSQLI_OPT_CONNECT_TIMEOUT, $seconds);
		}

		if (isset($data['init-command'])) {
			$cmd = $data['init-command'];
			if (! is_string($cmd) || empty($cmd)) {
				$err = 'MYSQLI_INIT_COMMAND must be an non empty string';
				throw new InvalidArgumentException($err);
			}
			$driver->options(MYSQLI_INIT_COMMAND, $cmd);
		}

		if (isset($data['is-local-infile'])) {
			$isLocal = (true === $data['is-local-infile']) ? true : false;
			$driver->options(MYSQLI_OPT_LOCAL_INFILE, $isLocal);
		}

		if (isset($data['read-default-file'])) {
			$file = $data['read-default-file'];
			if (! is_string($file) || empty($file)) {
				$err = 'MYSQLI_READ_DEFAULT_FILE must be a non empty string';
				throw new InvalidArgumentException($err);
			}
			$driver->options(MYSQLI_READ_DEFAULT_FILE, $file);
		}

		if (isset($data['read-default-group'])) {
			$group = $data['read-default-group'];
			if (! is_string($group) || empty($group)) {
				$err = 'MYSQLI_READ_DEFAULT_GROUP must be a non empty string';
				throw new InvalidArgumentException($err);
			}
			$driver->options(MYSQLI_READ_DEFAULT_GROUP, $file);
		}
	}

	/**
	 * @return	DbAdapterInterface
	 */
	public function createDbAdapter()
	{
		return new MysqliAdapter($this->getDriver());
	}

	/**
	 * @return	bool
	 */
	public function close()
	{
		if (! $this->isDriver() || ! $this->isConnected()) {
			return true;
		}
		
		$result = $this->getDriver()
					   ->close();

		if (false === $result) {
			return false;
		}

		$this->isConnected = false;
		return true;
	}

	/**
	 * @return	bool
	 */
	public function isConnected()
	{
		return $this->isConnected;
	}

	/**
	 * @return	ErrorItem | null 
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return bool
	 */
	public function isError()
	{
		return ! empty($this->error);
	}

	/**
	 * @param	string	$code
	 * @param	string	$text
	 * @return	null
	 */
	protected function setError($code, $text)
	{
		$this->error = array(
			'error-nbr' => $code,
			'error-text'  => $text 
		);
	}
}
