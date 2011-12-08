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
namespace TestFuel\TestCase;

use Appfuel\Db\DbManager,
	Appfuel\Framework\Registry,
    Appfuel\Framework\Exception,
    Appfuel\Db\Connection\DetailFactory as ConnFactory,
    Appfuel\Framework\Db\Connection\DetailFactoryInterface,
    Appfuel\Db\ConnectionDetailInterface;

/**
 */
class DbTestCase extends BaseTestCase 
{
    /**
     * Factory used to parse the connection string and create the connection
     * detail object used for appfuels unittest database
     * @var ConnFactory
     */
    protected $connFactory = null;

    /**
     * Connection detail object represents the connection to the database.
     * Appfuel's db module only works with this object
     * @var ConnectionDetailInterface
     */
    protected $connDetail = null;

    /**
     * @return  DbTestCase
     */
    public function __construct($name = null,
                                array $data = array(),
                                $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
		$connector = DbManager::getConnector();
		$master = $connector->getMaster();
		$this->setConnectionDetail($master->getConnectionDetail());
    }

    /**
     * @return  ConnectionDetailInterface
     */
    public function getConnectionDetail()
    {
        return $this->connDetail;
    }

    /**
     * @return  ConnectionDetailInterface
     */
    public function setConnectionDetail(ConnectionDetailInterface $detail)
    {
        $this->connDetail = $detail;
        return $this;
    }

}
