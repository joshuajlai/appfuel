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
namespace TestFuel\Test\Db;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\ConnectionDetail;

/**
 * The connection detail is a vendor agnostic value object used to hold the
 * connections detail. We will test this objects ability parse an array and
 * set the correct connection settings
 */
class ConnectionDetailTest extends BaseTestCase
{
	/**
	 * It is a InvalidArgumentException to have a host name that is empty
	 * or not a string so we designed this dataset to include invalid 
	 * host names
	 * @return	array
	 */
	public function provideInvalidHost()
	{
		$noHost = array('user'=>'me','name'=>'db', 'pass'=>'you', 'port'=>123);
		$emptyHost = array_merge($noHost, array('host' => ''));
		$objHost   = array_merge($noHost, array('host' => new StdClass()));
		$arrayHost = array_merge($noHost, array('host' => array(1,2,3)));
		$intHost   = array_merge($noHost, array('host' => 12345));
		return array(
			array($noHost),
			array($emptyHost),
			array($objHost),
			array($arrayHost),
			array($intHost),
		);
	}

	/**
	 * It is a InvalidArgumentException to have a database name that is empty
	 * or not a string so we designed this dataset to include invalid 
	 * database names
	 * @return	array
	 */
	public function provideInvalidName()
	{
		$noName = array('host'=>'us', 'user'=>'me','pass'=>'you', 'port'=>123);
		$emptyName = array_merge($noName, array('name' => ''));
		$objName   = array_merge($noName, array('name' => new StdClass()));
		$arrayName = array_merge($noName, array('name' => array(1,2,3)));
		$intName   = array_merge($noName, array('name' => 12345));
		return array(
			array($noName),
			array($emptyName),
			array($objName),
			array($arrayName),
			array($intName),
		);
	}

	/**
	 * It is a InvalidArgumentException to have a user name that is empty
	 * or not a string so we designed this dataset to include invalid 
	 * database names
	 * @return	array
	 */
	public function provideInvalidUser()
	{
		$noUser = array('host'=>'us', 'name'=>'me','pass'=>'you', 'port'=>123);
		$emptyUser = array_merge($noUser, array('user' => ''));
		$objUser   = array_merge($noUser, array('user' => new StdClass()));
		$arrayUser = array_merge($noUser, array('user' => array(1,2,3)));
		$intUser   = array_merge($noUser, array('user' => 12345));
		return array(
			array($noUser),
			array($emptyUser),
			array($objUser),
			array($arrayUser),
			array($intUser),
		);
	}

	/**
	 * It is a InvalidArgumentException to have password that is empty
	 * or not a string so we designed this dataset to include invalid 
	 * password. Now some may say you can have empty password, screw that
	 * if you don't want to use passwords use another framework or write
	 * your own ConnectionDetail
	 *
	 * @return	array
	 */
	public function provideInvalidPassword()
	{
		$noPass = array('host'=>'us', 'name'=>'me','user'=>'you', 'port'=>123);
		$emptyPass = array_merge($noPass, array('pass' => ''));
		$objPass   = array_merge($noPass, array('pass' => new StdClass()));
		$arrayPass = array_merge($noPass, array('pass' => array(1,2,3)));
		$intPass   = array_merge($noPass, array('pass' => 12345));
		return array(
			array($noPass),
			array($emptyPass),
			array($objPass),
			array($arrayPass),
			array($intPass),
		);
	}

	/**
	 * Since having an invalid port is not a RunTimeException, instead the
	 * default port of 3306 is used. This dataset is designed to have a 
	 * a variety of valid an invalid port values.
	 * @return	array
	 */
	public function providePortData()
	{
		$noPort = array('host'=>'us', 'user'=>'me','pass'=>'you','name'=>'db');
		$emptyPort  = array_merge($noPort, array('port' => ''));
		$objPort    = array_merge($noPort, array('port' => new StdClass()));
		$arrayPort  = array_merge($noPort, array('port' => array(1,2,3)));
		$strNbrPort = array_merge($noPort, array('port' => '12345'));
		$strPort    = array_merge($noPort, array('port' => 'this is a string'));
		$int0Port   = array_merge($noPort, array('port' => 0));
		$negIntPort = array_merge($noPort, array('port' => -33));
		$int1Port   = array_merge($noPort, array('port' => 1));
		$intPort    = array_merge($noPort, array('port' => 3308));
		return array(
			array($noPort, 3306),
			array($emptyPort, 3306),
			array($objPort, 3306),
			array($arrayPort, 3306),
			array($strNbrPort, 3306),
			array($strPort, 3306),
			array($int0Port, 3306),
			array($negIntPort, 3306),
			array($int1Port, 1),
			array($intPort, 3308) 
		);
	}

	/**
	 * Since having an invalid socket is not a InvalidArgumentException,instead 
	 * the no value will be stored. This dataset is designed to have a 
	 * a variety of valid an invalid socket values.
	 * @return	array
	 */
	public function provideSocketData()
	{
		$noSock = array('host'=>'us', 'user'=>'me','pass'=>'you','name'=>'db');
		$emptySock  = array_merge($noSock, array('socket' => ''));
		$objSock    = array_merge($noSock, array('socket' => new StdClass()));
		$arraySock  = array_merge($noSock, array('socket' => array(1,2,3)));
		$strNbrSock = array_merge($noSock, array('socket' => '12345'));
		$intSock    = array_merge($noSock, array('socket' => 3308));
		$validSock  = array_merge(
			$noSock, 
			array('socket' => '/tmp/mysql.sock')
		);
		return array(
			array($noSock,		null),
			array($emptySock,	null),
			array($objSock,		null),
			array($arraySock,	null),
			array($strNbrSock,	'12345'),
			array($intSock,		null),
			array($validSock,	'/tmp/mysql.sock'),
		);
	}
	/**
	 * @return	null
	 */
	public function testValid()
	{
		$params = array(
			'host' => 'localhost',
			'name' => 'my-db',
			'user' => 'my-user',
			'pass' => 'my-pass',
			'port' => 3307
		);
		$detail = new ConnectionDetail($params);
		$this->assertInstanceOf(
			'Appfuel\Db\ConnectionDetailInterface',
			$detail
		);
		$this->assertEquals($params['host'], $detail->getHost());
		$this->assertEquals($params['name'], $detail->getDbName());
		$this->assertEquals($params['user'], $detail->getUserName());
		$this->assertEquals($params['pass'], $detail->getPassword());
		$this->assertEquals($params['port'], $detail->getPort());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidHost
	 * @depends				testValid
	 * @return				null
	 */
	public function testConstructorHost_Failure($params)
	{
		$detail = new ConnectionDetail($params);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidName
	 * @depends				testValid
	 * @return				null
	 */
	public function testConstructorName_Failure($params)
	{
		$detail = new ConnectionDetail($params);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidUser
	 * @depends				testValid
	 * @return				null
	 */
	public function testConstructorUser_Failure($params)
	{
		$detail = new ConnectionDetail($params);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidPassword
	 * @depends				testValid
	 * @return				null
	 */
	public function testConstructorPass_Failure($params)
	{
		$detail = new ConnectionDetail($params);
	}

	/**
	 * @dataProvider		providePortData
	 * @depends				testValid
	 * @return				null
	 */
	public function testConstructorPortNumber($params, $expected)
	{
		$detail = new ConnectionDetail($params);
		$this->assertEquals($expected, $detail->getPort());
	}

	/**
	 * @dataProvider		provideSocketData
	 * @depends				testValid
	 * @return				null
	 */
	public function testConstructorSocket($params, $expected)
	{
		$detail = new ConnectionDetail($params);
		$this->assertEquals($expected, $detail->getSocket());
	}
}
