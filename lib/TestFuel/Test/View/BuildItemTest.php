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
namespace TestFuel\Test\View;

use StdClass,
	Appfuel\View\BuildItem,
	TestFuel\TestCase\BaseTestCase;

/**
 * A build item is a value object that holds the necessary information to 
 * build one template into another.
 */
class BuildItemTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var BuildItem
	 */
	protected $buildItem = null;

	/**
	 * Key of the source template, first parameter in constructor
	 * @var string
	 */
	protected $source = null;

	/**
	 * Key of the target template, second parameter in constructor
	 * @var string
	 */
	protected $target = null;

	/**
	 * assign label for target template, third parameter in constructor
	 * @var string
	 */
	protected $assign = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->source = 'my-source';
		$this->target = 'my-taget';
		$this->assign = 'my-assignment-label';
		$this->buildItem = new BuildItem(
			$this->source, 
			$this->target, 
			$this->assign
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->buildItem);
	}

	/**
	 * The value object holds the source template key, the target template
	 * key and the assignLabel
	 *
	 * @return null
	 */
	public function testImmutableMembers()
	{
		$this->assertEquals($this->source, $this->buildItem->getSource());
		$this->assertEquals($this->target, $this->buildItem->getTarget());
		$this->assertEquals($this->assign, $this->buildItem->getAssignLabel());
	}

	/**
	 * The result filter can hold either a string which is the name 
	 * of the callback method to use, an array where the first parameter
	 * is the object and the second parameter is the method to use or 
	 * an anomymous function that can directly filter the array
	 * 
	 * @return null
	 */
	public function testGetSetResultFilter()
	{
		$callback = 'my-method';
		$this->assertNull($this->buildItem->getResultFilter());
		$this->assertSame(
			$this->buildItem,
			$this->buildItem->setResultFilter($callback),
			'must use a fluent interface'
		);
		$this->assertEquals($callback, $this->buildItem->getResultFilter());

		$callback = array(new StdClass(), 'my-method');
		$this->assertSame(
			$this->buildItem,
			$this->buildItem->setResultFilter($callback),
			'must use a fluent interface'
		);
		$this->assertEquals($callback, $this->buildItem->getResultFilter());

		$callback = function($string) {
			return trim($string);
		};
		$this->assertSame(
			$this->buildItem,
			$this->buildItem->setResultFilter($callback),
			'must use a fluent interface'
		);
		$this->assertEquals($callback, $this->buildItem->getResultFilter());
	}

	/**
	 * Build Item holds information used to control how strict the building
	 * of a templates will be. When isSlientFail is false (default) then
	 * when the template does not exist build will ignore and move onto the
	 * next template. when isSlientFail is true it will throw an execption
	 * when the template can not be found
	 *
	 * @return null
	 */
	public function testIsEnableDisableSilentFail()
	{
		$this->assertTrue(
			$this->buildItem->isSilentFail(),
			'default value must be true'
		);

		$this->assertSame(
			$this->buildItem,
			$this->buildItem->disableSilentFail(),
			'must use fluent interface'
		);

		$this->assertFalse($this->buildItem->isSilentFail());

		$this->assertSame(
			$this->buildItem,
			$this->buildItem->enableSilentFail(),
			'must use fluent interface'
		);

		$this->assertTrue($this->buildItem->isSilentFail());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testEmptyScalarSource()
	{
		$source = '';
		$target = 'my-taget';
		$assign = 'my-assignment-label';
		$buildItem = new BuildItem($source, $target, $assign);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testNonScalarSourceArray()
	{
		$source = array(1,2,3,4);
		$target = 'my-taget';
		$assign = 'my-assignment-label';
		$buildItem = new BuildItem($source, $target, $assign);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testNonTargetSourceArray()
	{
		$source = 'my-source';
		$target = array(1,2,3,4);
		$assign = 'my-assignment-label';
		$buildItem = new BuildItem($source, $target, $assign);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testEmptyScalarTarget()
	{
		$source = 'my-source';
		$target = '';
		$assign = 'my-assignment-label';
		$buildItem = new BuildItem($source, $target, $assign);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testNonScalarAssignLabelArray()
	{
		$source = 'my-source';
		$target = 'my-target';
		$assign = array(1,2,3,4);
		$buildItem = new BuildItem($source, $target, $assign);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return null
	 */
	public function testEmptyScalarAssignLabel()
	{
		$source = 'my-source';
		$target = 'my-target';
		$assign = '';
		$buildItem = new BuildItem($source, $target, $assign);
	}
}
