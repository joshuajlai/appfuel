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
namespace Appfuel\Db\Mysql\Adapter;

use Mysqli,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Adapter\AdapterInterface,
	Appfuel\Framework\Db\Adapter\ErrorInterface,
	Appfuel\Framework\Db\Connection\ConnectionDetailInterface;

/**
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class MysqliAdapter implements AdapterInterface
{
	/**
	 * Handles all low level details pertaining to the server, this includes
	 * connecting, and getting the handle
	 * @var Server
	 */
	protected $server = null;

	/**
	 * Error value object containing the last know error
	 * @var ErrorInterface
	 */
	protected $error = null;

	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(Server $server)
	{
		$this->server = $server;
		$this->server->initialize();
	}

	/**
	 * @return	Server
	 */
	public function getServer()
	{
		return $this->server;
	}

	/**
	 * @return	ConnectionDetailInterface
	 */
	public function getConnectionDetail()
	{
		return $this->getServer()
					->getConnectionDetail();		
	}

	/**
	 * @return bool
	 */
	public function isConnected()
	{
		return $this->getServer()
					->isConnected();
	}

	/**
	 * Establish a connection to the database using the connection detail
	 * located in the server
	 *
	 * @return bool
	 */
	public function connect()
	{
		$server = $this->getServer();
		if ($server->isConnected()) {
			return true;
		}

		if (! $server->connect()) {
			$this->setError($server->getConnectionError());
			return false;		
		}

		return true;
	}

	/**
	 * Close the establish connection with the database
	 * 
	 * @return bool
	 */
	public function close()
	{
		$server = $this->getServer();
		if (! $server->isConnected()) {
			return true;
		}

		if (! $server->close()) {
			$this->setError($server->getConnectionError());
			return false;
		}

		return true;
	}

    /**
     * @return  ErrorInterface
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
		return $this->error instanceof Error;
	}

	/**
	 * @param	Error	$error
	 * @return	null
	 */
	protected function setError(Error $error)
	{
		$this->error = $error;
	}
}
