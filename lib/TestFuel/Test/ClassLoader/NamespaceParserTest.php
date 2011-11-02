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
namespace TestFuel\Test\ClassLoader;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\ClassLoader\NamespaceParser;

/**
 * The core responsibility is to turn either a php namespace or pear naming
 * scheme into a path to php
 */
class NamespaceParserTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideNamespaces()
	{
		$ds = DIRECTORY_SEPARATOR;
		return array(
			array('', false),
			array(' ', false),
			array("\t", false),
			array("\n", false),
			array(" \t\n", false),
			array('MyNameSpace', false),
			array('\MyNamespace', false),
			array('\MyNamespace\\', "MyNamespace{$ds}"),
			array('MyNamespace\\', "MyNamespace{$ds}"),
			array('My\Name\Space', "My{$ds}Name{$ds}Space"),
			array('My\Name\Space\\', "My{$ds}Name{$ds}Space{$ds}"),
			array('My\Name_Space', "My{$ds}Name{$ds}Space"),
			array("My_Name_Space", false),
			array("My_Name_Space\\", "My_Name_Space{$ds}"),
			array("\t My\Name\Space \n", "My{$ds}Name{$ds}Space"),
		);
	}
	
	/**
	 * @return	array
	 */
	public function providePearNames()
	{
		$ds = DIRECTORY_SEPARATOR;
		return array(
			array('', false),
			array(' ', false),
			array("\t", false),
			array("\n", false),
			array(" \t\n", false),
			array('MyName', 'MyName'),
			array('MyName_', "MyName{$ds}"),
			array(" MyName ", 'MyName'),
			array("\tMyName\n ", 'MyName'),
			array("My_Name", "My{$ds}Name"),
			array("My_Name_Space", "My{$ds}Name{$ds}Space"),
		);
	}

	/**
	 * @return array
	 */
	public function provideParseStrings()
	{
		$ds = DIRECTORY_SEPARATOR;
		return array(
			array('', false),
            array(' ', false),
            array("\t", false),
            array("\n", false),
            array(" \t\n", false),
			array('MyName', 'MyName'),
			array('MyName_', "MyName{$ds}"),
			array(" MyName ", 'MyName'),
			array("\tMyName\n ", 'MyName'),
			array("My_Name", "My{$ds}Name"),
			array("My_Name_Space", "My{$ds}Name{$ds}Space"),
			array('\MyNamespace\\', "MyNamespace{$ds}"),
			array('MyNamespace\\', "MyNamespace{$ds}"),
			array('My\Name\Space', "My{$ds}Name{$ds}Space"),
			array('My\Name\Space\\', "My{$ds}Name{$ds}Space{$ds}"),
			array('My\Name_Space', "My{$ds}Name{$ds}Space"),
			array("My_Name_Space\\", "My_Name_Space{$ds}"),
			array("\t My\Name\Space \n", "My{$ds}Name{$ds}Space"),
		);
	}
	
	/**
	 * @return array
	 */
	public function provideParseStringsWithExtensions()
	{
		$ds = DIRECTORY_SEPARATOR;
		return array(
			array('', false),
            array(' ', false),
            array("\t", false),
            array("\n", false),
            array(" \t\n", false),
			array('MyName', 'MyName.php'),
			array('MyName_', "MyName{$ds}.php"),
			array(" MyName ", 'MyName.php'),
			array("\tMyName\n ", 'MyName.php'),
			array("My_Name", "My{$ds}Name.php"),
			array("My_Name_Space", "My{$ds}Name{$ds}Space.php"),
			array('\MyNamespace\\', "MyNamespace{$ds}.php"),
			array('MyNamespace\\', "MyNamespace{$ds}.php"),
			array('My\Name\Space', "My{$ds}Name{$ds}Space.php"),
			array('My\Name\Space\\', "My{$ds}Name{$ds}Space{$ds}.php"),
			array('My\Name_Space', "My{$ds}Name{$ds}Space.php"),
			array("My_Name_Space\\", "My_Name_Space{$ds}.php"),
			array("\t My\Name\Space \n", "My{$ds}Name{$ds}Space.php"),
		);
	}


	/**
	 * @dataProvider	provideNamespaces
	 * @return			null
	 */
	public function testParseNs($input, $expected)
	{
		$result = NamespaceParser::parseNs($input);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @dataProvider	providePearNames
	 * @return			null
	 */
	public function testParsePear($input, $expected)
	{
		$result = NamespaceParser::parsePear($input);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @dataProvider	provideParseStrings
	 * @return			null
	 */
	public function testParseNoExtension($input, $expected)
	{
		$result = NamespaceParser::parse($input, false);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @dataProvider	provideParseStringsWithExtensions
	 * @return			null
	 */
	public function testParseWithExtension($input, $expected)
	{
		$result = NamespaceParser::parse($input);
		$this->assertEquals($expected, $result);
	}


}
