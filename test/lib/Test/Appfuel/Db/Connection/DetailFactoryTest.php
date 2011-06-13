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
namespace Test\Appfuel\Db\Connection;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Connection\DetailFactory,
	StdClass;

/**
 * The detail factory is used to create connection detail objects
 */
class DetailFactoryTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Parser
	 */
	protected $factory = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->factory = new DetailFactory();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->parser);
	}

	/**
	 * @return	array
	 */
	public function providerConnString()
	{
		return array(

		);
	}

	/**
	 * @return	null
	 */
	public function testConstructorGetParser()
	{
		$this->assertInstanceOf(
			'Appfuel\Db\Connection\Parser',
			$this->factory->getParser(),
			'default parser must be Appfuel\Db\Connection\Parser'
		);

		$interface ='Appfuel\Framework\Db\Connection\ParserInterface';
		$parser    = $this->getMock($interface);
		$factory = new DetailFactory($parser);
		$this->assertEquals($parser, $factory->getParser());	
	}

	/**
	 * @return null
	 */
	public function testCreateConnectionDetail()
	{
		$string = 'vendor=mysql;adapter=mysqli;host=my-host;username=me;' .
				  'dbname=my-db;password=pass;port=3306;socket=tmp/mysql.sock';

		$result = $this->factory->createConnectionDetail($string);
		$this->assertInstanceOf(
			'Appfuel\Db\Connection\ConnectionDetail',
			$result
		);
		
		$this->assertEquals('mysql',			$result->getVendor());
		$this->assertEquals('mysqli',			$result->getAdapter());
		$this->assertEquals('my-host',			$result->getHost());
		$this->assertEquals('me',				$result->getUserName());
		$this->assertEquals('pass',				$result->getPassword());
		$this->assertEquals(3306,				$result->getPort());
		$this->assertEquals('tmp/mysql.sock',	$result->getSocket());
		
	}

}
