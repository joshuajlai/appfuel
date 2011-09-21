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
	TestFuel\TestCase\BaseTestCase,
	Appfuel\App\Context\NullContext,
	Appfuel\App\Filter\FilterChain,
	Appfuel\App\Filter\InterceptingFilter,
	Appfuel\Framework\Context\ContextInterface;

/**
 * Controls the usage for all interceptiong filters
 */
class FilterChainTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var FilterManager
	 */
	protected $chain = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->chain = new FilterChain('pre');
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->chain = null;
	}

	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\App\Filter\FilterChainInterface',
			$this->chain
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorGetType()
	{
		$this->assertEquals('pre', $this->chain->getType());
	}

	/**
	 * @depends	testConstructorGetType
	 * @return	null
	 */ 
	public function testGetSetType()
	{
		$this->assertSame(
			$this->chain,
			$this->chain->setType('post'),
			'fluent interface'
		);
		$this->assertEquals('post', $this->chain->getType());

		$this->assertSame(
			$this->chain,
			$this->chain->setType('pre'),
			'fluent interface'
		);

		$this->assertEquals('pre', $this->chain->getType());

		$this->assertSame(
			$this->chain,
			$this->chain->setType('PRE'),
			'fluent interface'
		);
		$this->assertEquals('pre', $this->chain->getType());

		$this->assertSame(
			$this->chain,
			$this->chain->setType('POST'),
			'fluent interface'
		);
		$this->assertEquals('post', $this->chain->getType());
	}

	/**
	 * The head is the first intercept filter used when you apply the 
	 * filter chain
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetHead()
	{
		$this->assertNull($this->chain->getHead());
		
		$filterD = new PreFilterD();
		$this->assertSame(
			$this->chain,
			$this->chain->setHead($filterD),
			'uses a fluent interface'
		);
		$this->assertSame($filterD, $this->chain->getHead());
	}

	/**
	 * When no head exists and you add a filter that filter becomes head
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddFilterNoHead()
	{
		$this->assertNull($this->chain->getHead());
		$filterD = new PreFilterD();
		$this->assertSame(
			$this->chain,
			$this->chain->addFilter($filterD),
			'uses fluent interface'
		);
		$this->assertSame($filterD, $this->chain->getHead());

	}

	/**
	 * The filter filter will be head. Then head will be the next of the 
	 * second filter and the second filter will be assigned to head
	 *
	 * @depends	testAddFilterNoHead
	 * @return	null
	 */
	public function testAddTwoFilters()
	{
		$this->assertNull($this->chain->getHead());
		$filterD = new PreFilterD();
		$filterC = new PreFilterC();
		
		$this->chain->addFilter($filterD);
		$this->assertSame(
			$this->chain,
			$this->chain->addFilter($filterC),
			'uses fluent interface'
		);
		
		$head = $this->chain->getHead();
		$this->assertSame($filterC, $head);
		$this->assertSame($filterD, $head->getNext());
	}

	/**
	 * @depends	testAddTwoFilters
	 * @return	null
	 */
	public function testAddFourFilters()
	{
		$this->assertNull($this->chain->getHead());
		$filterD = new PreFilterD();
		$filterC = new PreFilterC();
		$filterB = new PreFilterB();
		$filterA = new PreFilterA();
		
		$this->chain->addFilter($filterD)
					->addFilter($filterC)
					->addFilter($filterB)
					->addFilter($filterA);
		
		$head = $this->chain->getHead();
		$this->assertSame($filterA, $head);

		$headNext = $head->getNext();
		$this->assertSame($filterB, $headNext);

		$headNextNext = $headNext->getNext();
		$this->assertSame($filterC, $headNextNext);

		$headNextNextNext = $headNextNext->getNext();
		$this->assertSame($filterD, $headNextNextNext);
	}

	/**
	 * @depends	testInterface
	 * @return null
	 */
	public function testApply()
	{
		$this->assertNull($this->chain->getHead());
		
		$in = 'Appfuel\Framework\App\Filter\InterceptingFilterInterface';
		$filter = $this->getMock($in);

		$result = 'return value';
		$filter->expects($this->once())
			   ->method('filter')
			   ->will($this->returnValue($result));

		$filter->expects($this->any())
			   ->method('getType')
			   ->will($this->returnValue('pre'));

		$this->chain->setHead($filter);
	
		$in = 'Appfuel\Framework\App\Context\ContextInterface';
		$context = $this->getMock($in);

		$this->assertEquals($result, $this->chain->apply($context));
	}

	/**
	 * @depends	testInterface
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetHead_PostTypePreChainFailure()
	{
		$in = 'Appfuel\Framework\App\Filter\InterceptingFilterInterface';
		$filter = $this->getMock($in);

		$filter->expects($this->any())
			   ->method('getType')
			   ->will($this->returnValue('post'));

		$this->chain->setHead($filter);
	}

	/**
	 * @depends	testInterface
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetHead_PreTypePostChainFailure()
	{
		$this->chain->setType('post');
		$in = 'Appfuel\Framework\App\Filter\InterceptingFilterInterface';
		$filter = $this->getMock($in);

		$filter->expects($this->any())
			   ->method('getType')
			   ->will($this->returnValue('pre'));

		$this->chain->setHead($filter);
	}

	/**
	 * @depends	testInterface
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetHead_NeitherPrePostInPostChainFailure()
	{
		$in = 'Appfuel\Framework\App\Filter\InterceptingFilterInterface';
		$filter = $this->getMock($in);

		$filter->expects($this->any())
			   ->method('getType')
			   ->will($this->returnValue('not-pre-or-post'));

		$this->chain->setHead($filter);
	}



	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetType_EmptyStringFailure()
	{
		$this->chain->setType('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetType_IntFailure()
	{
		$this->chain->setType(12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetType_ArrayFailure()
	{
		$this->chain->setType(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetType_ObjFailure()
	{
		$this->chain->setType(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetType_NotPrePostFailure()
	{
		$this->chain->setType('Not-Pre-OR-POST');
	}

	/**
	 * This will fail because we are trying to add a post filter to a pre
	 * pre filter chain
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testAddFilter_WrongTypeFailure()
	{
		$filter = new PostFilterA();
		$this->chain->addFilter($filter);
	}




}
