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
	 * 
	 * @var bool
	 */
	protected $isInitialized = false;

	/**
	 * Flag used to determine if a connection to the database has been 
	 * established. This connection is always done through mysqli_real_connect
	 * @var bool
	 */
	protected $isConnected = false;

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
        if ($this->isConnected()) {
            return true;
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
