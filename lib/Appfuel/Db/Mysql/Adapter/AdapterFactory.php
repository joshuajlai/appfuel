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

use mysqli,
	mysqli_stmt,
	\Exception as RootException,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Adapter\AdapterFactoryInterface,
	Appfuel\Framework\Db\Connection\ConnectionDetailInterface,
	Appfuel\Framework\Db\Connection\DetailFactoryInterface,
	Appfuel\Db\Connection\DetailFactory as ConnectionFactory;

/**
 * Create the MysqliAdapter using a ConnectionDetail
 */
class AdapterFactory implements AdapterFactoryInterface
{
	protected $connFactory = null;

	/**
	 * Assign connection factory used to build the connection detail object
	 * from a connection string
	 *
	 * @param	DetailFactoryInterface	$connFactory
	 * @return	AdapterFactory
	 */
	public function __construct(DetailFactoryInterface $connFactory = null)
	{
		if (null === $connFactory) {
			$connFactory = new ConnectionFactory();
		}

		$this->connFactory = $connFactory;
	}

	/**
	 * @return	DetailFactoryInterface
	 */
	public function getConnectionFactory()
	{
		return $this->connFactory;
	}

	/**
	 * @param	ConnectionDetailInterface	$conn
	 * @return	MysqliAdapter
	 */
	public function createAdapter(Connection $conn)
	{
		return new MysqliAdapter($conn->getHandle());
	}

	public function createConnection(ConnectionDetailInterface $connDetail,
									 mysqli $handle)
	{
		return new Connection($connDetail, $handle);
	}

	public function buildConnection($connString)
	{
		$connDetail = $this->getConnectionFactory()
						  ->createConnectionDetail();

		$handle = mysqli_init();
		return new Connection($connDetail, $handle);		
	}
}
