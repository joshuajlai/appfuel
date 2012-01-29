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
	Appfuel\Kernel\Mvc\InterceptChain,
	Appfuel\Kernel\Mvc\InterceptFilter,
	Appfuel\Kernel\Mvc\MvcContext,
	Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 */
class InterceptChainTestTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	InterceptChain
	 */
	protected $chain = null;

	/**
	 * Interface used to create mock objects
	 * @var string
	 */
	protected $contextInterface = null;

	/**
	 * Interface used to create mock objects
	 * @var string
	 */
	protected $filterInterface = null;

	/**
	 * ContextBuilder an immutable member passed into the constructor
	 * @var string
	 */
	protected $builder = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->filterInterface = 'Appfuel\Kernel\Mvc\InterceptFilterInterface';
		$this->contextInterface = 'Appfuel\Kernel\Mvc\MvcContextInterface';
		$builderInterface = 'Appfuel\Kernel\Mvc\ContextBuilderInterface';

		$this->builder = $this->getMock($builderInterface);	
		$this->chain = new InterceptChain($this->builder);	
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->chain = null;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\InterceptChainInterface',
			$this->chain
		);
		$this->assertSame($this->builder, $this->chain->getContextBuilder());
		$this->assertEquals(array(), $this->chain->getFilters());
		$this->assertFalse($this->chain->isFilters());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddFilterGetFiltersIsFilters()
	{
		$filter1 = $this->getMock($this->filterInterface);
		$this->assertSame($this->chain, $this->chain->addFilter($filter1));
		
		$expected = array($filter1);
		$this->assertTrue($this->chain->isFilters());
		$this->assertEquals($expected, $this->chain->getFilters());

		$filter2 = $this->getMock($this->filterInterface);
		$this->assertSame($this->chain, $this->chain->addFilter($filter2));
		
		$expected[] = $filter2;
		$this->assertTrue($this->chain->isFilters());
		$this->assertEquals($expected, $this->chain->getFilters());
			
		$filter3 = $this->getMock($this->filterInterface);
		$this->assertSame($this->chain, $this->chain->addFilter($filter3));
		
		$expected[] = $filter3;
		$this->assertTrue($this->chain->isFilters());
		$this->assertEquals($expected, $this->chain->getFilters());
	}

	/**
	 * @depends	testAddFilterGetFiltersIsFilters
	 * @return	null
	 */
	public function testAddFilterDoesNotPreventDuplicates()
	{
		$filter = $this->getMock($this->filterInterface);
		
		$this->chain->addFilter($filter)
					->addFilter($filter);

		$this->assertTrue($this->chain->isFilters());
		$expected = array($filter, $filter);
		$this->assertEquals($expected, $this->chain->getFilters());
	}

	/**
	 * @depends	testAddFilterGetFiltersIsFilters
	 * @return	null
	 */
	public function testLoadFilters()
	{
		$list1 = array(
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
		);

		$this->assertSame($this->chain, $this->chain->loadFilters($list1));
		$this->assertTrue($this->chain->isFilters());
		$this->assertEquals($list1, $this->chain->getFilters());

		/* load appends the filters in the list one at a time it does not
		 * clear
		 */
		$list2 = array(
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
		);
		$this->assertSame($this->chain, $this->chain->loadFilters($list2));
		$this->assertTrue($this->chain->isFilters());

		$expected = array_merge($list1, $list2);
		$this->assertEquals($expected, $this->chain->getFilters());
	}

	/**
	 * @depends	testLoadFilters
	 * @return	null
	 */
	public function testClearFilters()
	{
		$list1 = array(
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
		);
		$this->chain->loadFilters($list1);
		$this->assertTrue($this->chain->isFilters());
		
		$this->assertSame($this->chain, $this->chain->clearFilters());
		$this->assertFalse($this->chain->isFilters());
		$this->assertEquals(array(), $this->chain->getFilters());

	}

	/**
	 * While loadFilter appends to the list, setFilters replaces the list 
	 * with the one passed in
	 *
	 * @depends	testLoadFilters
	 * @return	null
	 */
	public function testSetFilters()
	{
		$list1 = array(
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
		);
		$this->chain->loadFilters($list1);
		$this->assertTrue($this->chain->isFilters());

		$list2 = array(
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
		);
		$this->assertNotEquals($list1, $list2);
		$this->assertSame($this->chain, $this->chain->setFilters($list2));
		$this->assertTrue($this->chain->isFilters());
		$this->assertEquals($list2, $this->chain->getFilters());	
	}

	/**
	 * @depends	testLoadFilters
	 * @return	null
	 */
	public function testSetFiltersWhenChainIsEmpty()
	{
		$list1 = array(
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
			$this->getMock($this->filterInterface),
		);
		$this->assertFalse($this->chain->isFilters());
		$this->assertSame($this->chain, $this->chain->setFilters($list1));
		$this->assertEquals($list1, $this->chain->getFilters());	
	}

	/**
	 * @depends	testLoadFilters
	 * @return	null
	 */
	public function testSetFiltersArgIsEmpty()
	{
		$list1 = array();
		$this->assertFalse($this->chain->isFilters());
		$this->assertSame($this->chain, $this->chain->setFilters($list1));
		$this->assertEquals($list1, $this->chain->getFilters());	
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testApplyFiltersWhenNoFilters()
	{
		$context = $this->getMock($this->contextInterface);
		$this->assertFalse($this->chain->isFilters());
	
		$result = $this->chain->applyFilters($context);
		$this->assertSame($context, $result);
	}

	/**
	 * The normal use case: filters act on the context, have no need to 
	 * re-route to another action and no need to break the chain. Thus the
	 * context that goes in is the context that is returned
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testApplyFiltersBreakChainFalseNoContextReplace()
	{
		$filter1 = $this->getMock($this->filterInterface);
		$filter1->expects($this->once())
				->method('isReplaceContext')
				->will($this->returnValue(false));
	
		$filter1->expects($this->once())
				->method('isBreakChain')
				->will($this->returnValue(false));


		$filter2 = $this->getMock($this->filterInterface);
		$filter2->expects($this->once())
				->method('isReplaceContext')
				->will($this->returnValue(false));
	
		$filter2->expects($this->once())
				->method('isBreakChain')
				->will($this->returnValue(false));

		$filter3 = $this->getMock($this->filterInterface);
		$filter3->expects($this->once())
				->method('isReplaceContext')
				->will($this->returnValue(false));
	
		$filter3->expects($this->once())
				->method('isBreakChain')
				->will($this->returnValue(false));

		$this->chain->setFilters(array($filter1, $filter2, $filter3));

		$context = $this->getMock($this->contextInterface);
		$result = $this->chain->applyFilters($context);
		$this->assertSame($context, $result);

	}

	/**
	 * For this test will have three call backs each adding something
	 * to the context. Success is defined as all assigments in the 
	 * context that is returned
	 *
	 * @return null
	 */
	public function testApplyFiltersChainIntact()
	{
		$cback1 = function($myContext, $builder) {
			$myContext->add('filter1', 'filter-result-1');
		};
		$filter1 = new InterceptFilter('pre', $cback1);

		$cback2 = function($myContext, $builder) {
			$myContext->add('filter2', 'filter-result-2');
		};
		$filter2 = new InterceptFilter('pre', $cback2);

		$cback3 = function($myContext, $builder) {
			$myContext->add('filter3', 'filter-result-3');
		};
		$filter3 = new InterceptFilter('pre', $cback3);
		$this->chain->setFilters(array($filter1, $filter2, $filter3));

		$input  = $this->getMock('Appfuel\Kernel\Mvc\AppInputInterface');
		$detail = $this->getMock('Appfuel\Kernel\Mvc\MvcRouteDetailInterface');
		
		$context = new MvcContext('html', 'my-route', $detail, $input);

		$result = $this->chain->applyFilters($context);
		$this->assertSame($context, $result);
		$this->assertEquals('filter-result-1', $context->get('filter1'));
		$this->assertEquals('filter-result-2', $context->get('filter2'));
		$this->assertEquals('filter-result-3', $context->get('filter3'));
	}
	
	/**
	 * This time the second filter will break the chain
	 * @return null
	 */
	public function testApplyFiltersChainBreakChain()
	{
		$cback1 = function($myContext, $builder) {
			$myContext->add('filter1', 'filter-result-1');
		};
		$filter1 = new InterceptFilter('pre', $cback1);

		$cback2 = function($myContext, $builder) {
			$myContext->add('filter2', 'filter-result-2');
			return array('is-break-chain' => true);
		};

		$filter2 = new InterceptFilter('pre', $cback2);

		$cback3 = function($myContext, $builder) {
			$myContext->add('filter3', 'filter-result-3');
		};
		$filter3 = new InterceptFilter('pre', $cback3);
		$this->chain->setFilters(array($filter1, $filter2, $filter3));

		$input  = $this->getMock('Appfuel\Kernel\Mvc\AppInputInterface');
		$detail = $this->getMock('Appfuel\Kernel\Mvc\MvcRouteDetailInterface');
		
		$context = new MvcContext('html', 'my-route', $detail, $input);

		$result = $this->chain->applyFilters($context);
		$this->assertSame($context, $result);
		$this->assertEquals('filter-result-1', $context->get('filter1'));
		$this->assertEquals('filter-result-2', $context->get('filter2'));
		$this->assertNull($context->get('filter3', null));
	}
	
	/**
	 * Now we will replace the context with a new context.
	 * @return null
	 */
	public function testApplyFiltersChainReplaceContext()
	{
		$input  = $this->getMock('Appfuel\Kernel\Mvc\AppInputInterface');
		$detail = $this->getMock('Appfuel\Kernel\Mvc\MvcRouteDetailInterface');
		
		$context = new MvcContext('html', 'my-route', $detail, $input);
		$replaceContext = new MvcContext('json', 'new-route', $detail, $input);


		$cback1 = function($myContext, $builder) {
			$myContext->add('filter1', 'filter-result-1');
		};
		$filter1 = new InterceptFilter('pre', $cback1);

		$cback2 = function($myContext, $builder) use ($replaceContext) {
			$replaceContext->add('filter2', 'filter-result-2');
			
		return array(
				'is-break-chain' => false,
				'replace-context' => $replaceContext
			);
		};

		$filter2 = new InterceptFilter('pre', $cback2);

		$cback3 = function($myContext, $builder) {
			$myContext->add('filter3', 'filter-result-3');
		};
		$filter3 = new InterceptFilter('pre', $cback3);
		$this->chain->setFilters(array($filter1, $filter2, $filter3));

		$result = $this->chain->applyFilters($context);
		$this->assertNotSame($context, $result);
		$this->assertSame($replaceContext, $result);
		$this->assertNull($replaceContext->get('filter1'));
		$this->assertEquals('filter-result-2', $replaceContext->get('filter2'));
		$this->assertEquals('filter-result-3', $replaceContext->get('filter3'));
	}

	/**
	 * Now we will replace the context with a new context and break the chain
	 * @return null
	 */
	public function testApplyFiltersChainReplaceContextBreakChain()
	{
		$input  = $this->getMock('Appfuel\Kernel\Mvc\AppInputInterface');
		$detail = $this->getMock('Appfuel\Kernel\Mvc\MvcRouteDetailInterface');
		
		$context = new MvcContext('html', 'my-route', $detail, $input);
		$replaceContext = new MvcContext('json', 'new-route', $detail, $input);


		$cback1 = function($myContext, $builder) {
			$myContext->add('filter1', 'filter-result-1');
		};
		$filter1 = new InterceptFilter('pre', $cback1);

		$cback2 = function($myContext, $builder) use ($replaceContext) {
			$replaceContext->add('filter2', 'filter-result-2');
			
			return array(
				'is-break-chain' => true,
				'replace-context' => $replaceContext
			);
		};

		$filter2 = new InterceptFilter('pre', $cback2);

		$cback3 = function($myContext, $builder) {
			$myContext->add('filter3', 'filter-result-3');
		};
		$filter3 = new InterceptFilter('pre', $cback3);
		$this->chain->setFilters(array($filter1, $filter2, $filter3));

		$result = $this->chain->applyFilters($context);
		$this->assertNotSame($context, $result);
		$this->assertSame($replaceContext, $result);
		$this->assertNull($replaceContext->get('filter1'));
		$this->assertEquals('filter-result-2', $replaceContext->get('filter2'));
		$this->assertNull($replaceContext->get('filter3'));
	}
}
