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
namespace TestFuel\Unit\Kernel\Kernel;

use Appfuel\Kernel\Mvc\AppInput,
	Appfuel\Kernel\Mvc\MvcContext,
	Appfuel\Kernel\Mvc\MvcContextBuilder,
	TestFuel\TestCase\ControllerTestCase;

/**
 * Test the ability for the builder to create request uri with its different
 * configurations (createRequestUri, setUri, useServerRequestUri, useUriString)
 * Also test the ability to create AppInput with its different configurations
 * (setInput, buildInputFromDefault, defineInputAs, createInput) Also test
 * the ability to set the error stack.
 */
class MvcContextBuilderTest extends ControllerTestCase
{
    /**
     * System under test
     * @var ContextBuilder
     */
    protected $builder = null;
	
	/**
	 * @var array
	 */
	protected $serverBk = null;
    
	/**
     * @return null
     */
    public function setUp()
    {
		$this->builder = new MvcContextBuilder();
    }

    /**
     * @return null
     */
    public function tearDown()
    {
		$this->builder = null;
    }

	/**
	 * @dataProvider	provideAllStringsIncludingCastable
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testSetViewCastableStrings($str)
	{
		$this->assertSame($this->builder, $this->builder->setView($str));
		$this->assertEquals((string)$str, $this->builder->getView());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideNoCastableStrings
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetViewNotCastableStrings($str)
	{
		$this->builder->setView($str);
	}
}
