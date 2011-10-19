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
	Appfuel\Db\Schema\ColumnParser,
	TestFuel\TestCase\BaseTestCase;

/**
 * Test parsing space delimited column definition into dictionaries
 */
class ColumnParserTest extends BaseTestCase
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
		$this->parser = new ColumnParser();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->parser = null;
	}

	/**
	 * @return	array
	 */
	public function provideColDefinitionNoIdentifierOnNames()
	{
		$name     = 'my_col';
		$newInput = 'int not null';
		return array(
			array('my_col int not null',		$name, $newInput),
			array("my_col\tint not null",		$name, $newInput),
			array("my_col \tint not null",		$name, $newInput),
			array('my_col    int not null',		$name, $newInput),
			array("my_col\t\tint not null",		$name, $newInput),
			array("my_col  \t\tint not null",	$name, $newInput),
			array(' my_col int not null',		$name, $newInput),
			array("\tmy_col int not null",		$name, $newInput),
			array(" \tmy_col int not null",		$name, $newInput),
			array("\t my_col int not null",		$name, $newInput),
			array(" \t my_col int not null",	$name, $newInput),
			array("\t\tmy_col int not null",	$name, $newInput),
			array("\t my_col\t int not null",	$name, $newInput),
		);
	}

	/**
	 * @return	array
	 */
	public function provideColDefinitionIdentifierOnNames()
	{
		$mysql	  = '`my col`';
		$double   = '"my col"';
		$single    = "'my col'";
		$newInput = 'int not null';
		return array(
			array('"my col" int not null',			$double, $newInput),
			array("'my col'\tint not null",			$single, $newInput),
			array("'my col' \tint not null",		$single, $newInput),
			array('"my col"    int not null',		$double, $newInput),
			array("'my col'\t\tint not null",		$single, $newInput),
			array("`my col`  \t\tint not null",		$mysql,  $newInput),
			array(' `my col` int not null',			$mysql,  $newInput),
			array("\t'my col' int not null",		$single, $newInput),
			array(" \t'my col' int not null",		$single, $newInput),
			array("\t 'my col' int not null",		$single, $newInput),
			array(" \t 'my col' int not null",		$single, $newInput),
			array("\t\t 'my col' int not null",		$single, $newInput),
			array("\t 'my col'\t int not null",		$single, $newInput),
			array('"my col"   int not null',		$double, $newInput),
			array("\t".'"my col"   int not null',	$double, $newInput),
		);
	}


	/**
	 * @return null
	 */
	public function testInterface()
	{
        $this->assertInstanceOf(
            'Appfuel\Framework\Db\Schema\ColumnParserInterface',
            $this->parser,
            'must implment this interface'
        );
	}

	/**
	 * @dataProvider	provideColDefinitionNoIdentifierOnNames
	 * @depends	testInterface
	 * @return	null
	 */
	public function testExtractColumnNameNoIdentifier($input, $col, $newInput)
	{
		$expected = array(
			'column-name'  => $col,
			'input-string' => $newInput
		);
		$this->assertEquals(
			$expected, 
			$this->parser->extractColumnName($input)
		);
	}
	
	/**
	 * @dataProvider	provideColDefinitionIdentifierOnNames
	 * @depends	testInterface
	 * @return	null
	 */
	public function testExtractColumnNameIdentifier($input, $col, $newInput)
	{
		$expected = array(
			'column-name'  => $col,
			'input-string' => $newInput
		);
		$this->assertEquals(
			$expected, 
			$this->parser->extractColumnName($input)
		);
	}

	/**
	 * 
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
}
