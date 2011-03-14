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
namespace Test\Appfuel\Stdlib\Autoload;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\Stdlib\BagInterface,
	Appfuel\Stdlib\Data\Bag;

/**
 * 
 */
class BagTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var Appfuel\Stdlib\Data\Bag
	 */
	protected $bag = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->bag = new Bag();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->bag);
	}

    /**
     * @return null
     */
    public function testConstructor()
    {
		$this->assertTrue(TRUE);
    }
}
