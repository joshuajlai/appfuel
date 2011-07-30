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
namespace Test\Appfuel\Db\Sql\Identifier;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Sql\Identifier\Sql92Reserved;

/**
 * Test capabilities of the binary expression class
 */
class Sql92ReservedTest extends ParentTestCase
{
	/**
	 * @return null
	 */
	public function testIsReservedKnownWords()
	{	
		$words = Sql92Reserved::getWords();
		$this->assertInternalType('array', $words);
		$this->assertGreaterThan(0, $words);

		foreach ($words as $word) {
			$this->assertTrue(
				Sql92Reserved::isReserved($word),
				"($word) should be true"
			);
		}
	}

	/**
	 * @return null
	 */
	public function testIsReservedFalseCases()
	{
		$this->assertFalse(Sql92Reserved::isReserved(''));
		$this->assertFalse(Sql92Reserved::isReserved(array(1,3,4)));
		$this->assertFalse(Sql92Reserved::isReserved(new StdClass()));
		$this->assertFalse(Sql92Reserved::isReserved(12345));
		$this->assertFalse(Sql92Reserved::isReserved('i_am_not_reserved'));
		
	}

}
