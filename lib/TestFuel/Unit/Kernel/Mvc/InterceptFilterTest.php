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
	 * @return null
	 */
	public function setUp()
	{
		$this->contextInterface = 'Appfuel\Kernel\Mvc\MvcContextInterface';
	
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
		$this->assertFalse($this->filter->isBreakChain());
		$this->assertFalse($this->filter->isReplaceContext());
		$this->assertNull($this->filter->getContextToReplace());
		$this->assertFalse($this->filter->isCallback());
		$this->assertNull($this->filter->getCallback());
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
		$callback = function ($context) {};

		$this->assertSame($this->filter, $this->filter->setCallback($callback));
		$this->assertSame($callback, $this->filter->getCallback());
		$this->assertTrue($this->filter->isCallback());
	}

	/**
	 * Callback method used in tesing get/set callback
	 * 
	 * @return	null
	 */
	public function filter($context)
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
		
		$result = $this->filter->apply($context);
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
	
		$callback = function($context) {
			return null;
		};

		$this->filter->setCallback($callback);
		$result = $this->filter->apply($context);

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
	
		$callback = function ($context) {
			return array('is-break-chain' => true);
		};
		$this->filter->setCallback($callback);
		$this->assertNull($this->filter->apply($context));
		$this->assertTrue($this->filter->isBreakChain());	
		$this->assertFalse($this->filter->isReplaceContext());
	}

	public function testApplyReplaceContextBreakChain()
	{
		$context = $this->getMock($this->contextInterface);

		$replacement = $this->getMock($this->contextInterface);
		$this->assertNotSame($context, $replacement);	
		$callback = function ($context) use ($replacement) {
			return array(
				'is-break-chain'  => true,
				'replace-context' => $replacement
			);
		};	

		$this->filter->setCallback($callback);
		$this->assertNull($this->filter->apply($context));
		$this->assertTrue($this->filter->isBreakChain());	
		$this->assertTrue($this->filter->isReplaceContext());
		
	}
}
