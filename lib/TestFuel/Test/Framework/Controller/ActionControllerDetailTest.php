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
namespace TestFuel\Test\Framework\Action;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Framework\Action\ActionControllerDetail;

/**
 * Test the value object to ensure the members are immutable
 */
class ActionControllerDetailTest extends BaseTestCase
{
    /**
     * @return null
     */
    public function testMembers()
    {
		$action    = 'Appfuel\Action\Error\Handler\Invalid';
		$subModule = 'Appfuel\Action\Error\Handler';
		$module    = 'Appfuel\Action\Error';
		$root      = 'Appfuel\Action';
		$ctrClass  = "$action\Controller";

		$detail = new ActionControllerDetail($action);
		$this->assertInstanceOf(
			'Appfuel\Framework\Action\ActionControllerDetailInterface',
			$detail,
			'route must implement the route interface'
		);
	
		$this->assertEquals($action,	$detail->getActionNamespace());
		$this->assertEquals($subModule, $detail->getSubModuleNamespace());
		$this->assertEquals($module, $detail->getModuleNamespace());
		$this->assertEquals($root, $detail->getRootNamespace());
		$this->assertEquals($ctrClass, $detail->getControllerClass());
    }
}

