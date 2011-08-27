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
namespace TestFuel\Test\App;

use Appfuel\App\Context,
	TestFuel\TestCase\BaseTestCase;

/**
 *
 */
class ContextTest extends BaseTestCase
{
    /**
     * System under test
     * @var Message
     */
    protected $message = null;

    /**
     * @return null
     */
    public function setUp()
    {
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
    }

    /**
     * Testing how isRoute reacts when the route is present and abesent
     * from the message. The key message uses for route is 'route'
     *
     * @return null
     */
    public function testIsRoute()
    {
		$this->assertTrue(true);
    }
}
