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
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	Appfuel\Kernel\Mvc\AppInput,
	Appfuel\Kernel\Mvc\AppContext,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataStructure\Dictionary;

/**
 * The request object was designed to service web,api and cli request
 */
class AppContextTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AppContext
	 */
	protected $context = null;

	/**
	 * First parameter in the constructor
	 * @var string
	 */
	protected $route = null;

	/**
	 * Second parameter in the constructor, used to indicate the strategy of
	 * the context (console|ajax|html)
	 * @var string
	 */
	protected $strategy = null;

	/**
	 * Third Paramter is the application input
	 * @var string
	 */
	protected $input = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->route = 'my-route';
		$this->strategy = 'console';
		$this->input = $this->getMock('Appfuel\Kernel\Mvc\AppInputInterface');
		$this->context = new AppContext(
			$this->route,
			$this->strategy, 
			$this->input
		);
	}

	/**
	 * Restore the super global data
	 * 
	 * @return null
	 */
	public function tearDown()
	{
		$this->context = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppContextInterface',
			$this->context
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testRoute()
	{
		$this->assertEquals($this->route, $this->context->getRoute());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testStrategy()
	{
		$this->assertEquals($this->strategy, $this->context->getStrategy());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetInput()
	{
		$this->assertSame($this->input, $this->context->getInput());
	}
	
	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructorStrategy_Failure($strategy)
	{
		$context = new AppContext($this->route, $strategy, $this->input);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAclCodes()
	{
		$this->assertEquals(array(), $this->context->getAclCodes());

		$code1 = 'admin';
		$this->assertFalse($this->context->isAclCode($code1));
		$this->assertSame(
			$this->context,
			$this->context->addAclCode($code1)
		);
		$expected = array($code1);
		$this->assertEquals($expected, $this->context->getAclCodes());
		$this->assertTrue($this->context->isAclCode($code1));
	
		$code2 = 'editor';
		$this->assertFalse($this->context->isAclCode($code2));
		$this->assertSame(
			$this->context,
			$this->context->addAclCode($code2)
		);
		$expected = array($code1, $code2);
		$this->assertEquals($expected, $this->context->getAclCodes());
		$this->assertTrue($this->context->isAclCode($code2));

		/* does no produce duplicates */
		$this->assertSame(
			$this->context,
			$this->context->addAclCode($code1)
		);	
		$this->assertEquals($expected, $this->context->getAclCodes());
		$this->assertTrue($this->context->isAclCode($code2));
		$this->assertTrue($this->context->isAclCode($code1));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends				testInterface
	 * @return				null
	 */
	public function testAddAclRoleCodeInvalidString($code)
	{
		$this->context->addAclCode($code);
	}
}
