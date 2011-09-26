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
	Example\App\Filter\InvalidTypeFilter,
	Appfuel\App\Context\NullContext,
	Appfuel\App\Filter\FilterManager,
	TestFuel\TestCase\BaseTestCase;

/**
 * Controls the usage for all interceptiong filters
 */
class FilterManager_FailuresTest extends BaseTestCase
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
	 * Can not use a post filter in place of a pre filter
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorPreNotPre()
	{
		$pre = $this->createMockFilterChain();
		$pre->expects($this->once())
			->method('getType')
			->will($this->returnValue('post'));

		$manager = new FilterManager($pre);
	}

	/**
	 * Anything other than pre will throw an error on the first param.
	 * This should not happen because filters protect against invalid types
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorPreNotValid()
	{
		$pre = $this->createMockFilterChain();
		$pre->expects($this->once())
			->method('getType')
			->will($this->returnValue('must-be-only-pre'));

		$manager = new FilterManager($pre);
	}

	/**
	 * Can not use a pre filter in place of a post filter
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorPostNotPost()
	{
		$post = $this->createMockFilterChain();
		$post->expects($this->once())
			->method('getType')
			->will($this->returnValue('pre'));

		$manager = new FilterManager(null, $post);
	}

	/**
	 * Anything other than pre will throw an error on the first param.
	 * This should not happen because filters protect against invalid types
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorPostNotValid()
	{
		$post = $this->createMockFilterChain();
		$post->expects($this->once())
			->method('getType')
			->will($this->returnValue('must-be-only-post'));

		$manager = new FilterManager(null, $post);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testLoadFilterWithObject()
	{
		$this->manager->loadFilters(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testLoadFilterWithInt()
	{
		$this->manager->loadFilters(12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testLoadFilterArrayItemEmptyString()
	{
		$filters = array('');
		$this->manager->loadFilters($filters);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testLoadFilterArrayItemInt()
	{
		$filters = array(1234);
		$this->manager->loadFilters($filters);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testLoadFilterArrayItemObject()
	{
		$filters = array(new StdClass());
		$this->manager->loadFilters($filters);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testLoadFilterArrayItemArray()
	{
		$filters = array(array(1,2,3));
		$this->manager->loadFilters($filters);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testLoadFilterDoesNotImplementInterface()
	{
		$filters = array('StdClass');
		$this->manager->loadFilters($filters);
	}

	/**
	 * This should never happen. I had to design a class to return a type
	 * that was not pre or post to prove this exception would throw
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testLoadFilterNotPrePost()
	{
		$filters = array('Example\App\Filter\InvalidTypeFilter');
		$this->manager->loadFilters($filters);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testAddFilterNotPrePost()
	{
		$this->manager->addFilter(new InvalidTypeFilter());
	}



}
