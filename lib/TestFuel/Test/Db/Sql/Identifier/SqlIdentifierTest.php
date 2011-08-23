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
namespace TestFuel\Test\Db\Sql\Identifier;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Sql\Identifier\SqlIdentifier,
	Appfuel\Db\Sql\Identifier\SqlReservedWords;

/**
 * Test capabilities of the binary expression class
 */
class SqlIdentifierTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	 SqlIdentifier
	 */
	protected $identifier = null;

	/**
	 * @var string
	 */
	protected $sqlName = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->sqlName = 'valid_table_name';
		$this->identifier = new SqlIdentifier($this->sqlName);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->identifier);
	}

	/**
	 * @return	array
	 */
	public function provideSql92ReservedWords()
	{
		$reserved = new SqlReservedWords();
		$words = $reserved->getWords();
	
		$params = array();
		foreach ($words as $word) {
			$params[] = array($word);
		}	
		return $params;
	}

	/**
	 * @return null
	 */
	public function testValidWord()
	{
		$this->assertEquals($this->sqlName, $this->identifier->getOperand());
	}
	
	/**
	 * The use of parentheses have been permanently disabled
	 *
	 * @return	null
	 */
	public function testParentheses()
	{
		$this->assertFalse($this->identifier->isParentheses());
		$this->assertSame(
			$this->identifier,
			$this->identifier->enableParentheses(),
			'should still work but does nothing'
		);
		$this->assertFalse($this->identifier->isParentheses());
	}

	/**
	 * @dataProvider		provideSql92ReservedWords
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSql92ReservedWordsNoQuotes($word)
	{
		$identifier = new SqlIdentifier($word);
	}

	/**
	 * Test all reserved words to ensure they are now all valid with quotes
	 *
	 * @dataProvider		provideSql92ReservedWords
	 * @return	null
	 */
	public function testSql92ReservedWordsWithQuotes($word)
	{	
		$word = '"'. $word . '"';
		$identifier = new SqlIdentifier($word);
		$this->assertEquals($word, $identifier->getOperand());
	}
}
