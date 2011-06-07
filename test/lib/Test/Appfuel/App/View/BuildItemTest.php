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
namespace Test\Appfuel\App\View;

use Test\AfTestCase as ParentTestCase,
	Appfuel\App\View\BuildItem,
	StdClass;

/**
 * A build item is a value object that holds the necessary information to 
 * build one template into another.
 */
class BuildItemTest extends ParentTestCase
{
	/**
	 * The value object holds the source template key, the target template
	 * key and the assignLabel
	 *
	 * @return null
	 */
	public function testValidProperties()
	{
		$source = 'my-source';
		$target = 'my-taget';
		$assign = 'my-assignment-label';
		$buildItem = new BuildItem($source, $target, $assign);

		$this->assertEquals($source, $buildItem->getSource());
		$this->assertEquals($target, $buildItem->getTarget());
		$this->assertEquals($assign, $buildItem->getAssignLabel());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
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
	 * @expectedException	Appfuel\Framework\Exception
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
	 * @expectedException	Appfuel\Framework\Exception
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
	 * @expectedException	Appfuel\Framework\Exception
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
	 * @expectedException	Appfuel\Framework\Exception
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
	 * @expectedException	Appfuel\Framework\Exception
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
