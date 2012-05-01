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
namespace TestFuel\Unit\App\Resource;

use TestFuel\TestCase\BaseTestCase,
	Appfuel\App\Resource\ResourceModule;

/**
 * This is a file list that only allows css files to be added
 */
class ResourceModuleTest extends BaseTestCase
{
	/**
	 * System Under Test
	 * @var ResourceModule
	 */
	protected $module =  null;

	/**
	 * List of information about the module
	 * @var array
	 */
	protected $data = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->data = array(
			'name' => 'my-module',
			'requires' => array('mod1', 'mod2', 'mod3')
		); 
		$this->module = new ResourceModule($this->data);
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->module = null;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\App\Resource\ResourceModuleInterface',
			$this->module
		);
		$this->assertFalse($this->module->isGroup());
		$this->assertEquals($this->data['name'], $this->module->getName());
		$this->assertFalse($this->module->isTheme());
		$this->assertEquals('js', $this->module->getType());
		$this->assertFalse($this->module->isLang());
		$this->assertEquals(array(), $this->module->getLang());
		$this->assertFalse($this->module->isAfter());
		$this->assertEquals(array(), $this->module->getAfter());
		$this->assertTrue($this->module->isDependencies());
		$this->assertEquals(
			$this->data['requires'], 
			$this->module->getDependencies()
		);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testLoadEmptyArray_Failure()
	{
		$this->module->load(array());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testModuleEmptyArray_Failure()
	{
		$module = new ResourceModule(array());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testModuleNoNameFailure_Failure()
	{
		$data = array(
			'type'		=> 'css',
			'depends'	=> array('mod1', 'mod2', 'mod3'),
			'lang'		=> array('en', 'fr')
		);
		$module = new ResourceModule($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testLoadNoNameFailure_Failure()
	{
		$data = array(
			'type'		=> 'css',
			'depends'	=> array('mod1', 'mod2', 'mod3'),
			'lang'		=> array('en', 'fr')
		);
		$this->module->load($data);
	}

	/**	
	 * @expectedException	InvalidArgumentException
	 * @dataProvider	provideEmptyStrings
	 * @return null
	 */	
	public function testNameOnlyModuleEmptyName_Failure($name)
	{
		$module = new ResourceModule(array('name' => $name));
	}

	/**	
	 * @dataProvider	provideNonEmptyStringsNoNumbers
	 * @return null
	 */	
	public function testNameOnlyModule($name)
	{
		$module = new ResourceModule(array('name' => $name));
		$this->assertEquals($name, $module->getName());
	}

	/**
	 * Any type that is not css (case insensitive) is js all lowwer case
	 *
	 * @return	null
	 */
	public function testTypeWithCss()
	{
		$data = array('name'=>'my-module','type' => 'Css');
		$module = new ResourceModule($data);
		$this->assertEquals('css', $module->getType());

		$data = array('name'=>'my-module','type' => 'CsS');
		$module = new ResourceModule($data);
		$this->assertEquals('css', $module->getType());

		$data = array('name'=>'my-module','type' => 'CSS');
		$module = new ResourceModule($data);
		$this->assertEquals('css', $module->getType());

		/* any type that is not css is js */
		$data = array('name'=>'my-module','type' => 'not-css');
		$module = new ResourceModule($data);
		$this->assertEquals('js', $module->getType());

		$data = array('name'=>'my-module','type' => '');
		$module = new ResourceModule($data);
		$this->assertEquals('js', $module->getType());
	
		$data = array('name'=>'my-module','type' => null);
		$module = new ResourceModule($data);
		$this->assertEquals('js', $module->getType());
	}

	/**
	 * @return null
	 */
	public function testIsTheme()
	{
		$data = array('name' => 'my-module');
		$module = new ResourceModule($data);
		$this->assertFalse($module->isTheme());

		$data = array('name' => 'my-module', 'skinnable' => null);
		$module = new ResourceModule($data);
		$this->assertFalse($module->isTheme());

		$data = array('name' => 'my-module', 'skinnable' => false);
		$module = new ResourceModule($data);
		$this->assertFalse($module->isTheme());


		/* only a strict true will toggle this */
		$data = array('name' => 'my-module', 'skinnable' => 'true');
		$module = new ResourceModule($data);
		$this->assertFalse($module->isTheme());

		$data = array('name' => 'my-module', 'skinnable' => true);
		$module = new ResourceModule($data);
		$this->assertTrue($module->isTheme());
	}

	/**
	 * @return	null
	 */
	public function testCssModule()
	{
		$data = array('name' => 'my-module', 'type' => 'css');
		$module = new ResourceModule($data);

		$this->assertFalse($module->isGroup());
		$this->assertEquals($data['name'], $module->getName());
		$this->assertFalse($module->isTheme());
		$this->assertEquals('css', $module->getType());
		$this->assertFalse($module->isLang());
		$this->assertEquals(array(), $module->getLang());
		$this->assertFalse($module->isAfter());
		$this->assertEquals(array(), $module->getAfter());
		$this->assertFalse($module->isDependencies());
		$this->assertEquals(array(), $module->getDependencies());
	}
}
