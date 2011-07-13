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
namespace Appfuel\Db\Handler;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\PoolInterface,
	Appfuel\Framework\Db\Connection\ConnectionInterface;

/**
 * The database pool holds connections for the handler and hide master/slave
 * so the handler does not need to know about them. We use single master, 
 * single slave because it works the easies with HA proxy and with systems
 * that have no replication
 */
class Pool implements PoolInterface
{
	/**
	 * @var	ConnectionInterface
	 */
	protected $master = null;
	
	/**
	 * @var ConnectionInterface
	 */
	protected $slave = null;

	/**
	 * @param	string	
	 * @return	bool
	 */
	public function addConnection(ConnectionInterface $conn, $type = null)
	{
		if (null === $type) {
			$type = 'master';
		}

		$err = "invalid type given must be (master|slave|null)";
		if (empty($type) || ! is_string($type)) {
			throw new Exception($err);
		}
		
		$type = strtolower($type);
		if ('master' === $type) {
			$this->setMaster($conn);
			return true;
		}

		if ('slave' === $type) {
			$this->setSlave($conn);
			return true;
		}

		/* did not use master or slave as type */
		throw new Exception($err);
	}

	/**
	 * @param	string	$type	(read|write|both|null)
	 * @return	ConnectionInterface | false on failure | null not initialized
	 */
	public function getConnection($type = null)
	{
		if (null === $type) {
			$type = 'write';
		}

		if (empty($type) || ! is_string($type)) {
			return false;
		}

		if ('write' === $type || 'both' === $type) {
			return $this->getMaster();
		}

		if ('read' === $type) {
			return $this->getSlave();
		}

		return false;
	}

	/**
	 * @return null
	 */
	public function shutdown()
	{
		if ($this->isMaster()) {
			$this->master->close();
			$this->master = null;
		}

		if ($this->isSlave()) {
			$this->slave->close();
			$this->slave = null;
		}
	}

	/**
	 * @return	ConnectionInterface | null
	 */
	public function getMaster()
	{
		return $this->master;
	}

	/**
	 * @param	ConnectionInterface		$conn
	 * @return	Pool
	 */
	public function setMaster(ConnectionInterface $conn)
	{
		$this->master = $conn;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isMaster()
	{
		return $this->master instanceof ConnectionInterface;
	}

	/**
	 * @return	ConnectionInterface
	 */
	public function getSlave()
	{
		return $this->slave;
	}

	/**
	 * @param	ConnectionInterface	$conn
	 * @return	null
	 */
	public function setSlave(ConnectionInterface $conn)
	{
		$this->slave = $conn;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isSlave()
	{
		return $this->slave instanceof ConnectionInterface;
	}
}
