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
namespace TestFuel\Test\Domain\Operation;

use TestFuel\TestCase\BaseTestCase,
	Appfuel\Domain\Operation\OperationModel,
	Appfuel\Framework\Action\ActionControllerDetail;

/**
 * Test only success cases for the operation model
 */
class OperationModelTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Error
	 */
	protected $model = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->model = new OperationModel();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->model);
	}

	/**
	 * @return	array
	 */
	public function provideValidModelData()
	{
		$data1 = array(
			'id'				=> 99,
			'name'				=> 'some-operation',
			'description'		=> 'this is an operation',
			'controllerDetail'	=> 'Root\Module\SubModule\Action',
			'accessPolicy'		=> 'public',
			'route'				=> 'some/url/routee',
			'defaultFormat'		=> 'html',
			'opClass'			=> 'business',
			'requestType'		=> 'http',
			'filters'			=> array(
				'pre'	=> array('filterA', 'filterB', 'filterC'),
				'post'	=> array('filterD', 'filterE', 'filterG')
			)
		);

		$data2 = $data1;
		$data3 = $data1;
		$data4 = $data1;
		$data5 = $data1;
		$data6 = $data1;
		
		/* include all possible correct values */
		$data2['accessPolicy']	= 'private';
		$data3['opClass']		= 'infra';
		$data4['opClass']		= 'ui';
		$data5['requestType']	= 'ajax';
		$data6['requestType']	= 'cli';

		return array(
			array($data1),
			array($data2),
			array($data3),
			array($data4),
			array($data5),
			array($data6)
		);
	}

	/**
	 * @return null
	 */
	public function testHasInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainModelInterface',
			$this->model
		);
	}

	/**
	 * @dataProvider	provideValidModelData
	 * @return	null
	 */
	public function testMarshal(array $data)
	{
		$this->assertSame($this->model, $this->model->_marshal($data));
		$this->assertEquals($data['id'], $this->model->getId());
		$this->assertEquals($data['name'], $this->model->getName());
		$this->assertEquals(
			$data['description'], 
			$this->model->getDescription()
		);

		$detail = new ActionControllerDetail($data['controllerDetail']);
		$this->assertEquals(
			$detail,
			$this->model->getControllerDetail()
		); 
		$this->assertEquals(
			$data['accessPolicy'], 
			$this->model->getAccessPolicy()
		);

		$this->assertEquals($data['route'], $this->model->getRoute());
		$this->assertEquals(
			$data['defaultFormat'], 
			$this->model->getDefaultFormat()
		);

		$this->assertEquals($data['opClass'], $this->model->getOpClass());
		$this->assertEquals(
			$data['requestType'], 
			$this->model->getRequestType()
		);
	
		$this->assertEquals($data['filters'], $this->model->getFilters());

		$state = $this->model->_getDomainState();
		$this->assertTrue($state->isMarshal());
	}

	/**
	 * @depends	testMarshal
	 * @return	null
	 */
	public function testAddGetPreFilters()
	{
		$filters = array(
			'pre'  => array(),
			'post' => array()
		);
		$this->assertEquals($filters, $this->model->getFilters());

		$this->assertNull($this->model->_getDomainState());

		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter1', 'pre'),
			'exposes a fluent interface'
		);

		$state = $this->model->_getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Orm\Domain\DomainState',
			$state
		);

		$this->assertTrue($state->isDirty());
		
		$expected = array('filter1');
		$this->assertEquals($expected, $this->model->getPreFilters());
	
		/* 
		 * will return everything in pre that getPreFilters returned 
		 * plus an empty post
		 */	
		$expected = array(
			'pre'  => $expected,
			'post' => array()
		);
		$this->assertEquals($expected, $this->model->getFilters());
	
		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter2', 'pre'),
			'exposes a fluent interface'
		);
		
		$expected = array('filter1', 'filter2');
		$this->assertEquals($expected, $this->model->getPreFilters());

		$expected = array(
			'pre'  => $expected,
			'post' => array()
		);
		$this->assertEquals($expected, $this->model->getFilters());
	
		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter3', 'pre'),
			'exposes a fluent interface'
		);
		
		$expected = array('filter1', 'filter2', 'filter3');
		$this->assertEquals($expected, $this->model->getPreFilters());

		$expected = array(
			'pre'  => $expected,
			'post' => array()
		);
		$this->assertEquals($expected, $this->model->getFilters());
	}

	/**
	 * @depends	testAddGetPreFilters
	 * @return	null
	 */
	public function testAddGetPostFilters()
	{
		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter1', 'post'),
			'exposes a fluent interface'
		);

		$state = $this->model->_getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Orm\Domain\DomainState',
			$state
		);

		$this->assertTrue($state->isDirty());
		
		$expected = array('filter1');
		$this->assertEquals($expected, $this->model->getPostFilters());
	
		/* 
		 * will return everything in pre that getPreFilters returned 
		 * plus an empty post
		 */	
		$expected = array(
			'pre'  => array(),
			'post' => $expected
		);
		$this->assertEquals($expected, $this->model->getFilters());
	
		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter2', 'post'),
			'exposes a fluent interface'
		);
		
		$expected = array('filter1', 'filter2');
		$this->assertEquals($expected, $this->model->getPostFilters());

		$expected = array(
			'pre'  => array(),
			'post' => $expected
		);
		$this->assertEquals($expected, $this->model->getFilters());
	
		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter3', 'post'),
			'exposes a fluent interface'
		);
		
		$expected = array('filter1', 'filter2', 'filter3');
		$this->assertEquals($expected, $this->model->getPostFilters());

		$expected = array(
			'pre'  => array(),
			'post' => $expected
		);
		$this->assertEquals($expected, $this->model->getFilters());
	}

	/**
	 * @depends	testAddGetPostFilters
	 * @return	null
	 */
	public function testAddPreAndPostFilters()
	{
		$this->model->addFilter('filter1', 'PRE')
					->addFilter('filter2', 'POST')
					->addFilter('filter3', 'pRe')
					->addFilter('filter4', 'POst')
					->addFilter('filter5', 'pre')
					->addFilter('filter6', 'post');

		$expectedPre = array('filter1', 'filter3', 'filter5');
		$this->assertEquals($expectedPre, $this->model->getPreFilters());

		$expectedPost = array('filter2', 'filter4', 'filter6');
		$this->assertEquals($expectedPost, $this->model->getPostFilters());

		$expected = array(
			'pre'	=> $expectedPre,
			'post'	=> $expectedPost
		);

		$this->assertEquals($expected, $this->model->getFilters());

	}

	/**
	 * @depends	testAddPreAndPostFilters
	 * @return	null
	 */
	public function testAddPrePostFilterWithDuplications()
	{
		$this->model->addFilter('filter1', 'pre')
					->addFilter('filter1', 'pre')
					->addFilter('filter2', 'post')
					->addFilter('filter2', 'post')
					->addFilter('filter3', 'pre')
					->addFilter('filter4', 'post')
					->addFilter('filter4', 'post')
					->addFilter('filter4', 'post')
					->addFilter('filter5', 'pre')
					->addFilter('filter5', 'pre')
					->addFilter('filter5', 'pre')
					->addFilter('filter5', 'pre')
					->addFilter('filter6', 'post');

		$expectedPre = array('filter1', 'filter3', 'filter5');
		$this->assertEquals($expectedPre, $this->model->getPreFilters());

		$expectedPost = array('filter2', 'filter4', 'filter6');
		$this->assertEquals($expectedPost, $this->model->getPostFilters());

		$expected = array(
			'pre'	=> $expectedPre,
			'post'	=> $expectedPost
		);

		$this->assertEquals($expected, $this->model->getFilters());
	}

	/**
	 * Each time you run setFilters it wipes out any old filters already added
	 * 
	 * @depends	testAddPreAndPostFilters
	 * @return	null
	 */
	public function testSetFiltersAlternateWays()
	{
		$filters = array(
			'pre'  => 'filter1',
			'post' => 'filter2'
		);
		$this->assertSame(
			$this->model,
			$this->model->setFilters($filters),
			'exposes fluent interface'
		);

		$expected = array(
			'pre'	=> array('filter1'),
			'post'	=> array('filter2')
		);

		$this->assertEquals($expected, $this->model->getFilters());

		$filters = array(
			'pre'  => 'filter1'
		);
		$this->assertSame(
			$this->model,
			$this->model->setFilters($filters),
			'exposes fluent interface'
		);
		$expected = array(
			'pre'	=> array('filter1'),
			'post'	=> array()
		);

		$this->assertEquals($expected, $this->model->getFilters());

		$filters = array(
			'post' => 'filter1'
		);
		$this->assertSame(
			$this->model,
			$this->model->setFilters($filters),
			'exposes fluent interface'
		);

		$expected = array(
			'pre'	=> array(),
			'post'	=> array('filter1')
		);

		$this->assertEquals($expected, $this->model->getFilters());

		/* an empty array is ignored */
		$this->assertSame(
			$this->model,
			$this->model->setFilters(array()),
			'exposes fluent interface'
		);

		$this->assertEquals($expected, $this->model->getFilters());


	}
}
