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
namespace Test\Appfuel\Db\Adapter;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Domain\Tree\NestedSetModel;

/**
 * Test the adapters ability to wrap mysqli
 */
class NestedSetModelTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var NestedSetModel
	 */
	protected $model = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->model = new NestedSetModel();
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
	public function provideValidModel()
	{
		$data = array(
			'id'			=> 99,
			'nodeParentId'	=> 88,
			'nodeLabel'		=> 'My Node',
			'nodeType'		=> 'anyKindOfNode',
			'leftNode'		=> 1,
			'rightNode'		=> 48,
		);

		return array(array($data));
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
	 * @dataProvider	provideValidModel
	 * @return	null
	 */
	public function testMarshal(array $data)
	{
		$this->assertSame($this->model, $this->model->_marshal($data));
		$this->assertEquals($data['id'], $this->model->getId());
		$this->assertEquals(
			$data['nodeParentId'], 
			$this->model->getNodeParentId()
		);
		$this->assertEquals($data['nodeLabel'], $this->model->getNodeLabel());
		$this->assertEquals($data['nodeType'], $this->model->getNodeType());
		$this->assertEquals($data['leftNode'], $this->model->getLeftNode());
		$this->assertEquals($data['rightNode'], $this->model->getRightNode());
	
		$state = $this->model->_getDomainState();	
		$this->assertTrue($state->isMarshal());
	}
}
