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
	Appfuel\Db\Sql\Identifier\SqlReservedWords;

/**
 * Test capabilities of the binary expression class
 */
class SqlReservedWordsTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var SqlReservedWords
	 */
	protected $reserved = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->reserved = new SqlReservedWords();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->reserved);
	}

	/**
	 * @return null
	 */
	public function testIsReservedKnownWords()
	{	
		$words = $this->reserved->getWords();
		$this->assertInternalType('array', $words);
		$this->assertGreaterThan(0, $words);

		foreach ($words as $word) {
			$this->assertTrue(
				$this->reserved->isReserved($word),
				"($word) should be true"
			);
		}
	}

	/**
	 * @return null
	 */
	public function testIsReservedFalseCases()
	{
		$this->assertFalse($this->reserved->isReserved(''));
		$this->assertFalse($this->reserved->isReserved(array(1,3,4)));
		$this->assertFalse($this->reserved->isReserved(new StdClass()));
		$this->assertFalse($this->reserved->isReserved(12345));
		$this->assertFalse($this->reserved->isReserved('i_am_not_reserved'));
	}

}
