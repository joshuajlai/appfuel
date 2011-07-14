<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test;

use Appfuel\App\Registry,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Connection\ConnectionDetailInterface,
	Appfuel\Framework\Db\Connection\DetailFactoryInterface,
	Appfuel\Db\Connection\DetailFactory as ConnFactory;


/**
 * All Appfuel test cases will extend this class which provides features like
 * path locations, backup/restore autoloader, backup/restore include paths. 
 */
class DbCase extends AfTestCase
{
	/**
	 * Factory used to parse the connection string and create the connection
	 * detail object used for appfuels unittest database
	 * @var	ConnFactory
	 */
	protected $connFactory = null;
	
	/**
	 * Connection string to appfuel's unittest database
	 * @var string
	 */
	protected $connString = null;
	
	/**
	 * Connection detail object represents the connection to the database.
	 * Appfuel's db module only works with this object
	 * @var ConnectionDetailInterface
	 */
	protected $connDetail = null;

	/**
	 * Factory used to create database adapter specific objects
	 * @var	AdapterFactoryInterface
	 */
	protected $adapterFactory = null;
	
	/**
	 * @return	DbTestCase
	 */
	public function __construct($name = null, 
								array $data = array(), 
								$dataName = '')
	{
		parent::__construct($name, $data, $dataName);

		$connString = Registry::get('db_unittest');
		if (empty($connString)) {
			throw new Exception("Db connection string empty can not proceed");
		}
		
		$connFactory = new ConnFactory();
		$this->setConnFactory($connFactory);

		$connDetail = $this->createConnDetail($connString);
		$this->setConnDetail($connDetail);
	}

	/**
	 * @return	ConnectionDetail
	 */
	public function createConnDetail($connStr)
	{
		return $this->getConnFactory()
					->createConnectionDetail($connStr);
	}

	public function createConnection()
	{
		$factory = $this->getAdapterFactory();
		$hdl = mysqli_init();
		return $this->factory->createConnection($this->getConnDetail(), $hdl);
	}

	/**
	 * @return	ConnFactory
	 */
	public function getConnFactory()
	{
		return $this->connFactory;
	}

	/**
	 * @param	DetailFactoryInterface	$factory
	 * @return	DbTestCase
	 */
	public function setConnFactory(DetailFactoryInterface $factory)
	{
		$this->connFactory = $factory;
		return $this;
	}

	/**
	 * @return	ConnectionDetailInterface
	 */
	public function getConnDetail()
	{
		return $this->connDetail;
	}

	/**
	 * @return	ConnectionDetailInterface
	 */
	public function setConnDetail(ConnectionDetailInterface $detail)
	{
		$this->connDetail = $detail;
		return $this;
	}

}
