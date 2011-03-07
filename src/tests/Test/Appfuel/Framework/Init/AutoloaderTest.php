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
namespace Test\Appfuel\Framework\Init;

use Appfuel\Framework\Init\Autoload;

/**
 * Test inialization of autoloader. This is an initialization strategy 
 */
class AutoloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System Under Test
     * @var \Appfuel\Framework\Init\Includepath
     */
    protected $autoload = NULL;

    /**
     * Clear out the include path so we can test it being set
     * @return void
     */
    public function setUp()
    {
        $this->autoload = new Autoload();
    }

    public function tearDown()
    {
        unset($this->autoload);
    }

	public function testOne()
	{
		$this->assertTrue(TRUE);
	}
}

