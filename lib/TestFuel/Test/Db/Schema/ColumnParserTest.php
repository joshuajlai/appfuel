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
     * @return  array
     */
    public function provideDataTypeValueNoParenthese()
    {
        $name     = 'integer';
        $newInput = 'not null default 9';
        return array(
            array('integer not null default 9', $name, $newInput),
            array(' integer  not null default 9 ', $name, $newInput),
            array("\tinteger  not null default 9", $name, $newInput),
            array("\t integer  not null default 9", $name, $newInput),
            array("\t integer\t  not null default 9", $name, $newInput),
            array("\t integer \t  not null default 9", $name, $newInput),
            array("\t\t\tinteger\t\t   not null default 9", $name, $newInput),
        );
    }

    /**
     * @return  array
     */
    public function provideDataTypeValueParentheses()
    {
        $name     = 'integer';
		$mod      = 4;
        $newInput = 'default 9';
        return array(
            array('integer(4) default 9',			 $name, $mod, $newInput),
            array(' integer(4)  default 9 ',		 $name, $mod, $newInput),
            array("\tinteger(4) default 9",			 $name, $mod, $newInput),
            array("\t integer(4) default 9",		 $name, $mod, $newInput),
            array("\t integer\t(4) default 9",		 $name, $mod, $newInput),
            array("\t integer \t(4) default 9",		 $name, $mod, $newInput),
            array("\t\t\tinteger\t\t(4)\tdefault 9", $name, $mod, $newInput),
            array('integer(4) default(9)',	$name, $mod, 'default(9)'),
            array('integer(4) default (9)',	$name, $mod, 'default (9)'),
            array("integer(4) \tdefault\t( 9 )", $name, $mod,"default\t( 9 )"),
            array("integer(4) default(\t9\t)", $name, $mod,"default(\t9\t)"),
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
	 * @depends	testInterface
	 * @return	null
	 */
	public function testExtractColumnNameEmptyString()
	{
		$this->assertFalse($this->parser->extractColumnName(''));
		$expected = "parse error: input must be a non empty string";
		$this->assertTrue($this->parser->isError());
		$this->assertEquals($expected, $this->parser->getError());

		$expected = "parse error: input string can not be all whitespaces";
		$this->assertFalse($this->parser->extractColumnName(' '));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals($expected, $this->parser->getError());

		$this->assertFalse($this->parser->extractColumnName("\t"));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals($expected, $this->parser->getError());

		$this->assertFalse($this->parser->extractColumnName("\t\t\t"));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals($expected, $this->parser->getError());

		$this->assertFalse($this->parser->extractColumnName(" \t \t \t "));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals($expected, $this->parser->getError());

		/* parser does not validate vendor specific rules it only pulls 
		 * out the column name right or wrong. Column validation is the
		 * reponsibility of another object.
		 */
		$this->parser->clearError();
		$result = $this->parser->extractColumnName('" "');
		$expected = array(
			'column-name'  => '" "',
			'input-string' => ''
		);
		$this->assertEquals($expected, $result);
		$this->assertFalse($this->parser->isError());
	
		$expected['column-name'] = "' '";	
		$result = $this->parser->extractColumnName("' '");
		$this->assertEquals($expected, $result);
		$this->assertFalse($this->parser->isError());
		
		$expected['column-name'] = "` `";	
		$result = $this->parser->extractColumnName("` `");
		$this->assertEquals($expected, $result);
		$this->assertFalse($this->parser->isError());

		/* when column length is 2 we know its an empty column name which
		 * is not allowed in any db vendor
		 */
		$this->parser->clearError();
		$expected = 'parse error: column name can not be empty';	
		$this->assertFalse($this->parser->extractColumnName('""'));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals($expected, $this->parser->getError());

		$this->assertFalse($this->parser->extractColumnName("''"));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals($expected, $this->parser->getError());

		$this->assertFalse($this->parser->extractColumnName("``"));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals($expected, $this->parser->getError());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testExtractColumnNameIncompleteIdentifier()
	{
		$col = '"col integer not null';
		$expected = 'parse error: no end identifier for -(")';
		$this->assertFalse($this->parser->extractColumnName($col));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals($expected, $this->parser->getError());

		$col = "'col integer not null";
		$expected = "parse error: no end identifier for -(')";
		$this->assertFalse($this->parser->extractColumnName($col));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals($expected, $this->parser->getError());

		$col = "`col integer not null";
		$expected = "parse error: no end identifier for -(`)";
		$this->assertFalse($this->parser->extractColumnName($col));
		$this->assertTrue($this->parser->isError());
		$this->assertEquals($expected, $this->parser->getError());
	}

	/**
	 * 
	 * @dataProvider	provideDataTypeValueNoParenthese
	 * @depends			testInterface
	 * @return			null
	 */
	public function testExtractDataTypeNoParenthese($str, $type, $newInput)
	{
		$this->parser->clearError();
		$result = $this->parser->extractDataType($str);
		$expected = array(
			'data-type'		=> $type,
			'modifier'		=> null,
			'input-string'  => $newInput,
		);
		$this->assertEquals($expected, $result);
	}

	/**
	 * 
	 * @dataProvider	provideDataTypeValueParentheses
	 * @depends			testInterface
	 * @return			null
	 */
	public function testExtractDataTypeParentheses($str,$type,$mod,$newInput)
	{
		$this->parser->clearError();
		$result = $this->parser->extractDataType($str);
		$expected = array(
			'data-type'		=> $type,
			'modifier'		=> $mod,
			'input-string'  => $newInput,
		);
		$this->assertEquals($expected, $result);
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
