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
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\InterceptFilter;

/**
 */
class InterceptFilterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	InterceptFilter
	 */
	protected $filter = null;

	/**
	 * Interface used to create mock objects
	 * @var string
	 */
	protected $contextInterface = null;

	/**
	 * Interface used to create mock objects
	 * @var string
	 */
	protected $builderInterface = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->contextInterface = 'Appfuel\Kernel\Mvc\MvcContextInterface';
		$this->builderInterface = 'Appfuel\Kernel\Mvc\ContextBuilderInterface';
	
		$this->filter = new InterceptFilter();	
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->filter = null;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\InterceptFilterInterface',
			$this->filter
		);
		$this->assertEquals('pre', $this->filter->getType());
		$this->assertTrue($this->filter->isPre());
		$this->assertFalse($this->filter->isPost());
		$this->assertFalse($this->filter->isBreakChain());
		$this->assertFalse($this->filter->isReplaceContext());
		$this->assertNull($this->filter->getContextToReplace());
		$this->assertFalse($this->filter->isCallback());
		$this->assertNull($this->filter->getCallback());
	}

	/**
	 * @depends		testInitialState
	 * @return		null
	 */
	public function testConstructorSetPost()
	{
		$filter = new InterceptFilter('post');

		$this->assertEquals('post', $filter->getType());
		$this->assertFalse($filter->isPre());
		$this->assertTrue($filter->isPost());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testMarkAsPrePostFilter()
	{
		$this->assertSame($this->filter, $this->filter->markAsPostFilter());
		$this->assertEquals('post', $this->filter->getType());
		$this->assertFalse($this->filter->isPre());
		$this->assertTrue($this->filter->isPost());

		$this->assertSame($this->filter, $this->filter->markAsPreFilter());
		$this->assertEquals('pre', $this->filter->getType());
		$this->assertTrue($this->filter->isPre());
		$this->assertFalse($this->filter->isPost());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testBreakFilterChainContinueToNextFilter()
	{
		$this->assertSame($this->filter, $this->filter->breakFilterChain());
		$this->assertTrue($this->filter->isBreakChain());
		
		$this->assertSame(
			$this->filter, 
			$this->filter->continueToNextFilter()
		);
		$this->assertFalse($this->filter->isBreakChain());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetSetContextToReplace()
	{
		$context = $this->getMock($this->contextInterface);
		$this->assertSame(
			$this->filter,
			$this->filter->setContextToReplace($context)
		);
		$this->assertTrue($this->filter->isReplaceContext());
		$this->assertSame($context, $this->filter->getContextToReplace());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetSetIsCallbackClosure()
	{
		$callback = function ($context, $builder) {};

		$this->assertSame($this->filter, $this->filter->setCallback($callback));
		$this->assertSame($callback, $this->filter->getCallback());
		$this->assertTrue($this->filter->isCallback());
	}

	/**
	 * Callback method used in tesing get/set callback
	 * 
	 * @return	null
	 */
	public function filter($context, $builder)
	{

	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetSetIsCallbackCallable()
	{
		$callback = array($this, 'filter');
		$this->assertSame($this->filter, $this->filter->setCallback($callback));
		$this->assertSame($callback, $this->filter->getCallback());
		$this->assertTrue($this->filter->isCallback());
	}

	/**
	 * @expectedException	InvalidArgumentException 
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testGetSetIsCallbackCallableNotFilter_Failure()
	{
		$callback = array($this, 'testGetSetIsCallbackClosure');
		$this->filter->setCallback($callback);
	}

	/**
	 * @expectedException	InvalidArgumentException 
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testGetSetIsCallbackCallableEmptyMethod_Failure()
	{
		$callback = array($this, '');
		$this->filter->setCallback($callback);
	}

	/**
	 * @depends		testInitialState
	 * @return		null
	 */
	public function testApplyNoCallback()
	{
		$context = $this->getMock($this->contextInterface);
		$builder = $this->getMock($this->builderInterface);
		
		$result = $this->filter->apply($context, $builder);
		$this->assertNull($result);
		$this->assertFalse($this->filter->isBreakChain());	
		$this->assertFalse($this->filter->isReplaceContext());
	}

	/**
	 * @depends		testInitialState
	 * @return		null
	 */
	public function testApplyClosureNoReturnValue()
	{
		$context = $this->getMock($this->contextInterface);
		$builder = $this->getMock($this->builderInterface);
	
		$callback = function($context, $builder) {
			return null;
		};

		$this->filter->setCallback($callback);
		$result = $this->filter->apply($context, $builder);

		$this->assertNull($result);
		$this->assertFalse($this->filter->isBreakChain());	
		$this->assertFalse($this->filter->isReplaceContext());
	}

	/**
	 * @depends		testInitialState
	 * @return		null
	 */
	public function testApplyClosureWithIsNextReturnTrue()
	{
		$context = $this->getMock($this->contextInterface);
		$builder = $this->getMock($this->builderInterface);
	
		$callback = function ($context, $builder) {
			return array('is-break-chain' => true);
		};
		$this->filter->setCallback($callback);
		$this->assertNull($this->filter->apply($context, $builder));
		$this->assertTrue($this->filter->isBreakChain());	
		$this->assertFalse($this->filter->isReplaceContext());
	}

	public function testApplyReplaceContextBreakChain()
	{
		$context = $this->getMock($this->contextInterface);
		$builder = $this->getMock($this->builderInterface);

		$replacement = $this->getMock($this->contextInterface);
		$this->assertNotSame($context, $replacement);	
		$callback = function ($context, $builder) use ($replacement) {
			return array(
				'is-break-chain'  => true,
				'replace-context' => $replacement
			);
		};	

		$this->filter->setCallback($callback);
		$this->assertNull($this->filter->apply($context, $builder));
		$this->assertTrue($this->filter->isBreakChain());	
		$this->assertTrue($this->filter->isReplaceContext());
		
	}
}
