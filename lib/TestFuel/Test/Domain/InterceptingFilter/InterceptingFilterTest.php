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
namespace TestFuel\Test\Domain\Action;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Domain\InterceptFilter\InterceptFilterDomain;

/**
 * Test the action domain describes the action controller
 */
class InterceptingFilterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var ActionDomain
	 */
	protected $filter = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->filter = new InterceptFilterDomain();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->filter = null;
	}

	/**
	 * @return	array
	 */
	public function provideValidModelData()
	{
		$data = array(
			array(
				'id'		  => 99,
				'key'		  => 'filterKeyA',
				'type'		  => 'pre',
				'description' => 'this is pre filter'
			),
			array(
				'id'		  => 55,
				'key'		  => 'filterKeyB',
				'type'		  => 'post',
				'description' => 'this is post filter'
			),
		);
					

		return array($data);
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainModelInterface',
			$this->filter
		);

		$in  = 'Appfuel\Framework\Domain\InterceptFilter\InterceptFilterDomain';
		$in .= 'Interface';
		$this->assertInstanceOf($in, $this->filter);
	}

	/**
	 * @dataProvider	provideValidModelData
	 * @return	null
	 */
	public function testMarshal(array $data)
	{
		$this->assertSame($this->filter, $this->filter->_marshal($data));
		$this->assertEquals($data['id'], $this->filter->getId());
		$this->assertEquals($data['key'], $this->filter->getKey());
		$this->assertEquals(
			$data['description'], 
			$this->filter->getDescription()
		);
		$type = strtolower($data['type']);
		$this->assertEquals($type, $this->filter->getType());

		$state = $this->filter->_getDomainState();
		$this->assertEquals('marshal', $state->getState());
		$this->assertTrue($state->isMarshal());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testKey()
	{
		$this->assertNull($this->filter->getKey());
	
		$key = 'filter_key';
		$this->assertSame($this->filter, $this->filter->setKey($key));
		$this->assertEquals($key, $this->filter->getKey());	
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testType()
	{
		$this->assertNull($this->filter->getType());
	
		$type = 'pre';
		$this->assertSame($this->filter, $this->filter->setType($type));
		$this->assertEquals($type, $this->filter->getType());	

		$type = 'post';
		$this->assertSame($this->filter, $this->filter->setType($type));
		$this->assertEquals($type, $this->filter->getType());	

		$type = 'Pre';
		$this->assertSame($this->filter, $this->filter->setType($type));
		$this->assertEquals(strtolower($type), $this->filter->getType());	

		$type = 'PRE';
		$this->assertSame($this->filter, $this->filter->setType($type));
		$this->assertEquals(strtolower($type), $this->filter->getType());	

		$type = 'Post';
		$this->assertSame($this->filter, $this->filter->setType($type));
		$this->assertEquals(strtolower($type), $this->filter->getType());	

		$type = 'POST';
		$this->assertSame($this->filter, $this->filter->setType($type));
		$this->assertEquals(strtolower($type), $this->filter->getType());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDescription()
	{
		$this->assertNull($this->filter->getDescription());
	
		$text = 'this is some filter';
		$this->assertSame(
			$this->filter, 
			$this->filter->setDescription($text)
		);
		$this->assertEquals($text, $this->filter->getDescription());

		/* empty string will work */
		$text = '';
		$this->assertSame(
			$this->filter, 
			$this->filter->setDescription($text)
		);
		$this->assertEquals($text, $this->filter->getDescription());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testKey_EmptyStringFailure()
	{
		$this->filter->setKey('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testKey_IntFailure()
	{
		$this->filter->setKey(12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testKey_ArrayFailure()
	{
		$this->filter->setKey(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testKey_ObjectFailure()
	{
		$this->filter->setKey(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testType_EmptyStringFailure()
	{
		$this->filter->setType('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testType_IntFailure()
	{
		$this->filter->setType(12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testType_ArrayFailure()
	{
		$this->filter->setType(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testType_ObjectFailure()
	{
		$this->filter->setType(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testType_NoPreOrPostFailure()
	{
		$this->filter->setType('not-pre-or-post');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testDescription_IntFailure()
	{
		$this->filter->setDescription(1234);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testDescription_ArrayFailure()
	{
		$this->filter->setDescription(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depend	testInterface
	 * @return	null
	 */
	public function testDescription_ObjectFailure()
	{
		$this->filter->setDescription(new StdClass());
	}









}
