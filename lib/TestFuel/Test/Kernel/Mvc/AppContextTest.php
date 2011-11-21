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
namespace TestFuel\Test\Kernel\Mvc;

use StdClass,
	Appfuel\Error\ErrorStack,
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
	 * First Paramter is the application input
	 * @var string
	 */
	protected $input = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->input = $this->getMock('Appfuel\Kernel\Mvc\AppInputInterface');
		$this->context = new AppContext($this->input);
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
	public function testGetInput()
	{
		$this->assertSame($this->input, $this->context->getInput());
	}

	/**
	 * @depends	testInterface
	 */
	public function testErrorStack()
	{
		/* default error stack */
		$stack = $this->context->getErrorStack();
		$this->assertInstanceOf(
			'Appfuel\Error\ErrorStack',
			$stack
		);
		$this->assertFalse($this->context->isError());

		$stack = $this->getMock('Appfuel\Error\ErrorStackInterface');
		$context = new AppContext($this->input, $stack);
		$this->assertSame($stack, $context->getErrorStack());

		/* the setter is public so you can swap it out */
		$this->assertSame(
			$this->context,
			$this->context->setErrorStack($stack)
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIsErrorAddErrorAndGetErrorString()
	{
		/* default error stack */
		$stack = $this->context->getErrorStack();
		$this->assertInstanceOf(
			'Appfuel\Error\ErrorStack',
			$stack
		);
		$this->assertFalse($this->context->isError());


		$msg1  = 'my message';
		$code1 = 500;
		$this->assertSame(
			$this->context,
			$this->context->addError($msg1, $code1)
		);
		$this->assertTrue($this->context->isError());
		
		$result   = $this->context->getErrorString();
		$expected = 'Error: my message';
		$this->assertEquals($expected, $result);

		$msg2  = 'my other message';
		$this->assertSame(
			$this->context,
			$this->context->addError($msg2)
		);
		$this->assertTrue($this->context->isError());
	
		$result   = $this->context->getErrorString();
		$expected = 'Error: my message my other message';
		$this->assertEquals($expected, $result);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAclRoleCodes()
	{
		$this->assertEquals(array(), $this->context->getAclRoleCodes());

		$code1 = 'admin';
		$this->assertFalse($this->context->isAclRoleCode($code1));
		$this->assertSame(
			$this->context,
			$this->context->addAclRoleCode($code1)
		);
		$expected = array($code1);
		$this->assertEquals($expected, $this->context->getAclRoleCodes());
		$this->assertTrue($this->context->isAclRoleCode($code1));
	
		$code2 = 'editor';
		$this->assertFalse($this->context->isAclRoleCode($code2));
		$this->assertSame(
			$this->context,
			$this->context->addAclRoleCode($code2)
		);
		$expected = array($code1, $code2);
		$this->assertEquals($expected, $this->context->getAclRoleCodes());
		$this->assertTrue($this->context->isAclRoleCode($code2));

		/* does no produce duplicates */
		$this->assertSame(
			$this->context,
			$this->context->addAclRoleCode($code1)
		);	
		$this->assertEquals($expected, $this->context->getAclRoleCodes());
		$this->assertTrue($this->context->isAclRoleCode($code2));
		$this->assertTrue($this->context->isAclRoleCode($code1));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends				testInterface
	 * @return				null
	 */
	public function testAddAclRoleCodeInvalidString($code)
	{
		$this->context->addAclRoleCode($code);
	}
}
