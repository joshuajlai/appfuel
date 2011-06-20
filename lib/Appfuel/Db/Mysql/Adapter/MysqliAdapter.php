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

	public function connect()
	{
		$server = $this->getServer();
		if ($server->isConnected()) {
			return true;
		}

		if (! $server->isHandle()) {
			$server->initialize();
		}

	}

    /**
     * Creates an adapter error with mysqli error number and text
     *
     * @param   Mysqli  $handle
     * @return  Error
     */
    public function createMysqliError(Mysqli $handle)
    {
        return new Error($handle->errno, $handle->error);
    }

    /**
     * @return  bool
     */
    public function isConnected()
    {
        return $this->isConnected;
    }

    /**
     * @return 
     */
    public function clearHandle()
    {
        $this->handle = null;
    }

    /**
     * @return  ErrorInterface
     */
    public function getError()
    {
        return $this->error;
    }
}
