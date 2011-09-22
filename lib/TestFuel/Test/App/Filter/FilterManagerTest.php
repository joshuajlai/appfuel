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
		$methods   = array('getType','apply','getHead','setHead','addFilter'); 
		
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
}
