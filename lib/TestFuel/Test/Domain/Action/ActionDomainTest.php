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

use TestFuel\TestCase\BaseTestCase,
	Appfuel\Domain\Action\ActionDomain;

/**
 * Test the action domain describes the action controller
 */
class ActionDomainTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var ActionDomain
	 */
	protected $action = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->action = new ActionDomain();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->action = null;
	}

	/**
	 * @return	array
	 */
	public function provideValidModelData()
	{
		$data = array(
			'id'			=> 99,
			'namespace'		=> 'My\\Action\\Namespace',
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
			$this->action
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\Domain\Action\ActionDomainInterface',
			$this->action
		);
	}

	/**
	 * @dataProvider	provideValidModelData
	 * @return	null
	 */
	public function testMarshal(array $data)
	{
		$this->assertSame($this->action, $this->action->_marshal($data));
		$this->assertEquals($data['id'], $this->action->getId());
		$this->assertEquals($data['namespace'], $this->action->getNamespace());

		$state = $this->action->_getDomainState();
		$this->assertEquals('marshal', $state->getState());
		$this->assertTrue($state->isMarshal());
	}
}
