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
namespace Appfuel\MsgBroker\Amqp;

use Appfuel\Framework\Exception,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectorInterface;

/**
 * The connector is a value object hold all the connection details
 */
class AmqpConnector implements AmqpConnectorInterface
{
    /**
     * Host address for AMQP server
     * @var string
     */
    protected $host = NULL;

    /**
     * Port for AMQP server
     * @var string
     */
    protected $port = NULL;

    /**
     * User nome for rabbit node
     * @var string
     */
    protected $user = NULL;

    /**
     * Password for rabbit node
     * @var string
     */
    protected $password = NULL;

    /**
     * Virtual host for rabbit node
     * @var string
     */
    protected $vhost = null;

	/**
	 * @return	AmqpConnector
	 */
	public function __construct(array $data)
	{
        $err = 'Connector Error:';
        if (! isset($data['host']) ||
              empty($data['host']) || ! is_string($data['host'])) {
            throw new Exception("$err host must be none empty string");
        }
        $this->host = $data['host'];

        if (! isset($data['port']) || empty($data['port'])) {
            $port = 5672;
        }
        else {
            $port = $data['port'];
        }

        if (! is_int($port) || $port < 0) {
            throw new Exception("$err port must be a positive integer");
        }
        $this->port = $port;

        if (! isset($data['user']) ||
              empty($data['user']) || ! is_string($data['user'])) {
            throw new Exception("$err user must be none empty string");
        }
        $this->user = $data['user'];

        if (! isset($data['password']) ||
              empty($data['password']) || ! is_scalar($data['password'])) {
            throw new Exception("$err password must be none empty string");
        }
        $this->password = $data['password'];

        if (! isset($data['vhost']) ||
              empty($data['vhost']) || ! is_string($data['vhost'])) {
            throw new Exception("$err vhost must be none empty string");
        }
        $this->vhost = $data['vhost'];
	}

    /**
     * @return  string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return  string
     */
    public function getVirtualHost()
    {
        return $this->vhost;
    }
}
