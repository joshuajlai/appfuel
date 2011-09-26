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
namespace TestFuel\Test\Filter;

use StdClass,
	Example\App\Filter\PreFilterA,
	Example\App\Filter\PreFilterB,
	Example\App\Filter\PreFilterC,
	Example\App\Filter\PreFilterD,
	Example\App\Filter\PostFilterA,
	Example\App\Filter\PostFilterB,
	Example\App\Filter\PostFilterC,
	Appfuel\App\Context\NullContext,
	Appfuel\App\Filter\FilterManager,
	TestFuel\TestCase\BaseTestCase;

/**
 * Controls the usage for all interceptiong filters
 */
class FilterManagerTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var FilterManager
	 */
	protected $manager = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->manager	= new FilterManager();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->manager = null;
	}

	/**
	 * @return	FilterChainInterface
	 */
	public function createMockFilterChain()
	{
		$interface = 'Appfuel\Framework\App\Filter\FilterChainInterface';
		$methods   = array(
			'hasFilters',
			'getType',
			'apply',
			'getHead',
			'setHead',
			'addFilter'
		); 
		
		return $this->getMockBuilder($interface)
						 ->setConstructorArgs(array('pre'))
						 ->setMethods($methods)
						 ->getMock();
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\App\Filter\FilterManagerInterface',
			$this->manager
		);
	}

	/**
	 * When no values are given in the constructor then the filter manager
	 * will automatically create the pre is post
	 * 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorDefaultValues()
	{
		$pre = $this->manager->getPreChain();
		$this->assertInstanceOf(
			'Appfuel\App\Filter\FilterChain',
			$pre
		);
		$this->assertEquals('pre', $pre->getType());

		$post = $this->manager->getPostChain();
		$this->assertInstanceOf(
			'Appfuel\App\Filter\FilterChain',
			$post
		);
		$this->assertEquals('post', $post->getType());

	}

	/**
	 * @depends	testConstructorDefaultValues
	 * @return	null
	 */
	public function testConstructorPre()
	{
		$preChain = $this->createMockFilterChain();
		$preChain->expects($this->any())
				 ->method('getType')
				 ->will($this->returnValue('pre'));
	
		$manager = new FilterManager($preChain);
		$this->assertSame($preChain, $manager->getPreChain());

		$post = $manager->getPostChain();
		$this->assertInstanceOf(
			'Appfuel\App\Filter\FilterChain',
			$post
		);
		$this->assertEquals('post', $post->getType());
	}

	/**
	 * @depends	testConstructorDefaultValues
	 * @return	null
	 */
	public function testConstructorPost()
	{
		$postChain = $this->createMockFilterChain();
		$postChain->expects($this->any())
				 ->method('getType')
				 ->will($this->returnValue('post'));
	
		$manager = new FilterManager(null, $postChain);
		$this->assertSame($postChain, $manager->getPostChain());

		$pre = $manager->getPreChain();
		$this->assertInstanceOf(
			'Appfuel\App\Filter\FilterChain',
			$pre
		);
		$this->assertEquals('pre', $pre->getType());
	}

	/**
	 * @depends	testConstructorDefaultValues
	 * @return	null
	 */
	public function testConstructorPostPre()
	{
		$postChain = $this->createMockFilterChain();
		$postChain->expects($this->any())
				 ->method('getType')
				 ->will($this->returnValue('post'));
	
		$preChain = $this->createMockFilterChain();
		$preChain->expects($this->any())
				 ->method('getType')
				 ->will($this->returnValue('pre'));
	
		$manager = new FilterManager($preChain, $postChain);
		$this->assertSame($preChain, $manager->getPreChain());
		$this->assertSame($postChain, $manager->getPostChain());
	}

	/**
	 * Filters have been created in advance for the purposes of testing. These
	 * example namespace do exist
	 *
	 * @depends	testConstructorDefaultValues
	 * @return	null
	 */
	public function testLoadFilterSingleStringPre()
	{
		$class = 'Example\App\Filter\PreFilterA';
		$this->assertSame(
			$this->manager,
			$this->manager->loadFilters($class),
			'uses a fluent interface'
		);

		$chain = $this->manager->getPreChain();
		$this->assertTrue($chain->hasFilters());
		
		$filter = $chain->getHead();
		$this->assertInstanceOf($class, $filter);
		$this->assertFalse($filter->isNext());
	}

	/**
	 * Filters have been created in advance for the purposes of testing. These
	 * example namespace do exist
	 *
	 * @depends	testConstructorDefaultValues
	 * @return	null
	 */
	public function testLoadFilterSingleStringPost()
	{
		$class = 'Example\App\Filter\PostFilterA';
		$this->assertSame(
			$this->manager,
			$this->manager->loadFilters($class),
			'uses a fluent interface'
		);

		$chain = $this->manager->getPostChain();
		$this->assertTrue($chain->hasFilters());
		
		$filter = $chain->getHead();
		$this->assertInstanceOf($class, $filter);
		$this->assertFalse($filter->isNext());
	}

	/**
	 * @depends	testConstructorDefaultValues
	 * @return	null
	 */
	public function testLoadFiltersOnlyPreFilters()
	{
		$filters = array(
			'Example\App\Filter\PreFilterA',
			'Example\App\Filter\PreFilterB',
			'Example\App\Filter\PreFilterC',
			'Example\App\Filter\PreFilterD',
		);

		$this->assertSame(
			$this->manager,
			$this->manager->loadFilters($filters),
			'uses a fluent interface'
		);
		
		$chain = $this->manager->getPreChain();
		$this->assertTrue($chain->hasFilters());

		$head = $chain->getHead();
		$this->assertInstanceOf($filters[0], $head);

		$filterB = $head->getNext();
		$this->assertInstanceOf($filters[1], $filterB);
		
		$filterC = $filterB->getNext();
		$this->assertInstanceOf($filters[2], $filterC);

		$filterD = $filterC->getNext();
		$this->assertInstanceOf($filters[3], $filterD);
		$this->assertNull($filterD->getNext());

	}

	/**
	 * @depends	testConstructorDefaultValues
	 * @return	null
	 */
	public function testLoadFiltersOnlyPostFilters()
	{
		$filters = array(
			'Example\App\Filter\PostFilterA',
			'Example\App\Filter\PostFilterB',
			'Example\App\Filter\PostFilterC',
		);

		$this->assertSame(
			$this->manager,
			$this->manager->loadFilters($filters),
			'uses a fluent interface'
		);
		
		$chain = $this->manager->getPostChain();
		$this->assertTrue($chain->hasFilters());

		$head = $chain->getHead();
		$this->assertInstanceOf($filters[0], $head);

		$filterB = $head->getNext();
		$this->assertInstanceOf($filters[1], $filterB);
		
		$filterC = $filterB->getNext();
		$this->assertInstanceOf($filters[2], $filterC);
		$this->assertNull($filterC->getNext());
	}

	/**
	 * Since each filter knows what type (pre|post) it is the filter manager
	 * will know which chain to put it in. This means the order of pre and post
	 * can be intermixed and for each type its first in first out stack.
	 *
	 * @depends	testConstructorDefaultValues
	 * @return	null
	 */
	public function testLoadFiltersPrePostFilters()
	{
		$filters = array(
			'Example\App\Filter\PreFilterA',
			'Example\App\Filter\PostFilterA',
			'Example\App\Filter\PreFilterB',
			'Example\App\Filter\PostFilterB',
			'Example\App\Filter\PreFilterC',
			'Example\App\Filter\PreFilterD',
			'Example\App\Filter\PostFilterC',
		);

		$this->assertSame(
			$this->manager,
			$this->manager->loadFilters($filters),
			'uses a fluent interface'
		);
		
		$chain = $this->manager->getPostChain();
		$this->assertTrue($chain->hasFilters());

		$head = $chain->getHead();
		$this->assertInstanceOf($filters[1], $head);

		$filterB = $head->getNext();
		$this->assertInstanceOf($filters[3], $filterB);
		
		$filterC = $filterB->getNext();
		$this->assertInstanceOf($filters[6], $filterC);
		$this->assertNull($filterC->getNext());

		$chain = $this->manager->getPreChain();
		$this->assertTrue($chain->hasFilters());

		$head = $chain->getHead();
		$this->assertInstanceOf($filters[0], $head);

		$filterB = $head->getNext();
		$this->assertInstanceOf($filters[2], $filterB);
		
		$filterC = $filterB->getNext();
		$this->assertInstanceOf($filters[4], $filterC);

		$filterD = $filterC->getNext();
		$this->assertInstanceOf($filters[5], $filterD);
		$this->assertNull($filterD->getNext());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddFilterPre()
	{
		$filterA = new PreFilterA();
		$this->assertSame(
			$this->manager,
			$this->manager->addFilter($filterA),
			'uses fluent interface'
		);
		$chain = $this->manager->getPreChain();
		$this->assertTrue($chain->hasFilters());
		
		$head = $chain->getHead();
		$this->assertSame($filterA, $head);

		$filterB = new PreFilterB();
		$this->assertSame(
			$this->manager,
			$this->manager->addFilter($filterB),
			'uses fluent interface'
		);

		$head = $chain->getHead();
		$this->assertSame($filterB, $head);
		$this->assertSame($filterA, $head->getNext());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddFilterPost()
	{
		$filterA = new PostFilterA();
		$this->assertSame(
			$this->manager,
			$this->manager->addFilter($filterA),
			'uses fluent interface'
		);
		$chain = $this->manager->getPostChain();
		$this->assertTrue($chain->hasFilters());
		
		$head = $chain->getHead();
		$this->assertSame($filterA, $head);

		$filterB = new PostFilterB();
		$this->assertSame(
			$this->manager,
			$this->manager->addFilter($filterB),
			'uses fluent interface'
		);

		$head = $chain->getHead();
		$this->assertSame($filterB, $head);
		$this->assertSame($filterA, $head->getNext());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testApplyPreFiltersWithNoFilters()
	{
		$context = new NullContext();
		$chain   = $this->manager->getPreChain();
		$this->assertFalse($chain->hasFilters());
		
		$result = $this->manager->applyPreFilters($context);
		$this->assertSame($context, $result);
		$this->assertEquals(0, $result->count());
	}

	/**
	 * This filter is designed to produce a known result that we can test
	 * agaist. It will add to the context dictionary 
	 * test-var => first-pre-filterA
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testApplyPreFilterSingleFilter()
	{
		$context = new NullContext();
		$filters = 'Example\App\Filter\PreFilterA';

		$this->manager->loadFilters($filters);
		$chain   = $this->manager->getPreChain();
		$this->assertTrue($chain->hasFilters());
		
		$result = $this->manager->applyPreFilters($context);
		$this->assertSame($context, $result);
		$this->assertEquals(1, $result->count());
		
		$var = $context->get('test-var');
		$this->assertEquals('first-pre-filterA', $var);
	}

	/**
	 * Each filter is designed to concatenate on to the string the 
	 * the first filter put in
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testApplyPreFilterManyFilters()
	{
		$context = new NullContext();
		$filters = array(
			'Example\App\Filter\PreFilterA',
			'Example\App\Filter\PreFilterB',
			'Example\App\Filter\PreFilterC',
		);

		$this->manager->loadFilters($filters);
		$chain   = $this->manager->getPreChain();
		$this->assertTrue($chain->hasFilters());
		
		$result = $this->manager->applyPreFilters($context);
		$this->assertSame($context, $result);
		$this->assertEquals(1, $result->count());

		$expected = 'first-pre-filterA:second-pre-filterB:third-pre-filterC';
		$var = $context->get('test-var');
		$this->assertEquals($expected, $var);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testApplyPostFiltersWithNoFilters()
	{
		$context = new NullContext();
		$chain   = $this->manager->getPostChain();
		$this->assertFalse($chain->hasFilters());
		
		$result = $this->manager->applyPostFilters($context);
		$this->assertSame($context, $result);
		$this->assertEquals(0, $result->count());
	}

	/**
	 * This filter is designed to produce a known result that we can test
	 * agaist. It will add to the context dictionary 
	 * test-var => first-post-filterA
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testApplyPostFilterSingleFilter()
	{
		$context = new NullContext();
		$filters = 'Example\App\Filter\PostFilterA';

		$this->manager->loadFilters($filters);
		$chain   = $this->manager->getPostChain();
		$this->assertTrue($chain->hasFilters());
		
		$result = $this->manager->applyPostFilters($context);
		$this->assertSame($context, $result);
		$this->assertEquals(1, $result->count());
		
		$var = $context->get('test-var');
		$this->assertEquals('first-post-filterA', $var);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testApplyPostFilterManyFilters()
	{
		$context = new NullContext();
		$filters = array(
			'Example\App\Filter\PostFilterA',
			'Example\App\Filter\PostFilterB',
			'Example\App\Filter\PostFilterC',
		);

		$this->manager->loadFilters($filters);
		$chain = $this->manager->getPostChain();
		$this->assertTrue($chain->hasFilters());
		
		$result = $this->manager->applyPostFilters($context);
		$this->assertSame($context, $result);
		$this->assertEquals(1, $result->count());

		$expected = 'first-post-filterA:second-post-filterB:third-post-filterC';
		$var = $context->get('test-var');
		$this->assertEquals($expected, $var);
	}
}
