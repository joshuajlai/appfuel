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
	Appfuel\Db\Connection\Parser,
	StdClass;

/**
 * Parser will parse a connection string into its name value pairs
 */
class ParserTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Parser
	 */
	protected $parser = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->parser = new Parser();
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
			array('vendor=mysql;adapter=mysqli;host=my-host;username=me;' .
				  'password=pass;port=3306;socket=tmp/mysql.sock')

		);
	}

	/**
	 * Test the parser with a connection string that has all values and
	 * all values are valid. The second parameter determines if an array
	 * is returned or a dictionary. We will test the default which is 
	 * a dictionary
	 *
	 * @dataProvider	providerConnString
	 * @return null
	 */
	public function testParseAllValuesDictionary($string)
	{

		$result = $this->parser->parse($string);
		$this->assertInstanceOf(
			'Appfuel\Data\Dictionary',
			$result
		);

		$this->assertEquals('mysql',			$result->get('vendor'));
		$this->assertEquals('mysqli',			$result->get('adapter'));
		$this->assertEquals('my-host',			$result->get('host'));
		$this->assertEquals('me',				$result->get('username'));
		$this->assertEquals('pass',				$result->get('password'));
		$this->assertEquals(3306,				$result->get('port'));
		$this->assertEquals('tmp/mysql.sock',	$result->get('socket'));
	}

	/**
	 * @dataProvider	providerConnString
	 * @return null
	 */
	public function testParseAllValuesArray($string)
	{

		$isDictionary = false;
		$result = $this->parser->parse($string, $isDictionary);
		$this->assertInternalType('array', $result);

		$this->assertArrayHasKey('vendor',$result);
		$this->assertEquals('mysql',$result['vendor']);
		
		$this->assertArrayHasKey('adapter',$result);
		$this->assertEquals('mysqli', $result['adapter']);
		
		$this->assertArrayHasKey('host',$result);
		$this->assertEquals('my-host', $result['host']);
		
		$this->assertArrayHasKey('username',$result);
		$this->assertEquals('me', $result['username']);
		
		$this->assertArrayHasKey('password',$result);
		$this->assertEquals('pass', $result['password']);
		
		$this->assertArrayHasKey('port',$result);
		$this->assertEquals(3306, $result['port']);

		$this->assertArrayHasKey('socket',$result);
		$this->assertEquals('tmp/mysql.sock', $result['socket']);
	}

	/**
	 * This parser will only parses string delimited with a ';'. This shows
	 * that when no ';' is found only one string will be parsed for the '='
	 * this will leave the string 
	 *
	 * vendor=mysql,adapter=mysqli,host=my-host
	 * 
	 * this is then exploded for '=' which gives an  array
	 * 
	 * array('vendor', 'mysql,adapter','mysqli,host','my-host') only item
	 * 
	 * 0, 1 are selected for the key\value this leaves
	 * vendor and 'mysql,adapter'
	 * 
	 * @return	null
	 */
	public function testParseMalformedString()
	{
		$string = 'vendor=mysql,adapter=mysqli,host=my-host';
		$result = $this->parser->parse($string);
		$this->assertInstanceOf(
			'Appfuel\Data\Dictionary',
			$result
		);
			
		$this->assertEquals('mysql,adapter', $result->get('vendor'));
	}

	public function testParseSingleString()
	{
		$string = 'vendor';
		$result = $this->parser->parse($string);
		$this->assertInstanceOf(
			'Appfuel\Data\Dictionary',
			$result
		);
		$this->assertEquals(0, $result->count());	
	}

	/**
	 * @return null
	 */
	public function testParseEmptyString()
	{
		$this->assertFalse($this->parser->parse(''));
	}

	/**
	 * @return null
	 */
	public function testParseArray()
	{
		$this->assertFalse($this->parser->parse(array(1,2,3,4)));
	}

	/**
	 * @return null
	 */
	public function testParseInt()
	{
		$this->assertFalse($this->parser->parse(1234));
	}

	/**
	 * @return null
	 */
	public function testParseObject()
	{
		$this->assertFalse($this->parser->parse(new StdClass()));
	}


}
