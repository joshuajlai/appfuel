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
namespace TestFuel\Test\Db\Schema;

use StdClass,
	Appfuel\Db\Schema\StringParser,
	TestFuel\TestCase\BaseTestCase;

/**
 * Test parsing space delimited column definition into dictionaries
 */
class StringParserTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var StringParser
	 */
	protected $parser = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->parser = new StringParser();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->parser = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
        $this->assertInstanceOf(
            'Appfuel\Framework\Db\Schema\StringParserInterface',
            $this->parser,
            'must implment this interface'
        );
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function xtestParserColumn_EmptyString()
	{
		$this->assertFalse($this->parser->parseColumn(''));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals(
			'parse error: column definition string can not be empty',
			$this->parser->getError()
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function xtestParseColumn_NoColumnNoType()
	{
		$column = 'column_name ';
		$expectedError  = 'parse error: column definition ';
		$expectedError .= 'must have at least name and data type';

		$this->assertFalse($this->parser->parseColumn($column));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals(
			$expectedError,
			$this->parser->getError()
		);
	}

	public function testOne()
	{
		$col = "col \tenum('a \) and b or ', 'c and d') not null default 99999999999999999999999999999 primary key";

		$results = $this->parser->parseColumn($col);
	}

}
