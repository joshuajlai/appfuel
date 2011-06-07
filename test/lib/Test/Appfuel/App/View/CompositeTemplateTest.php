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
	Appfuel\App\View\CompositeTemplate,
	Appfuel\App\View\Template,
	StdClass;

/**
 * Since a composit is a template that can hold other templates we will
 * be testing its ability to add remove get and build templates
 *
 */
class CompositeTemplateTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Template
	 */
	protected $template = null;

	/**
	 * Name of the template passed into the constructor
	 * @var string
	 */
	protected $templateName = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->templateName = 'my-template';
		$this->template = new CompositeTemplate($this->templateName);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->template);
	}

	/**
	 * @return null
	 */
	public function testGetAddRemoveExistsTemplate()
	{
		$key_1 = 'other-template';
		$this->assertFalse($this->template->templateExists($key_1));
		$this->assertFalse($this->template->getTemplate($key_1));

		$interface  = 'Appfuel\Framework\App\View\TemplateInterface';
		$template_1 = $this->getMockBuilder($interface)
						   ->disableOriginalConstructor()
						   ->getMock();

		$this->assertSame(
			$this->template,
			$this->template->addTemplate($key_1, $template_1),
			'must use a fluent interface'
		);

		$this->assertTrue($this->template->templateExists($key_1));
		$this->assertSame($template_1, $this->template->getTemplate($key_1));

		$key_2      = 'my-other-template';
		$template_2 = $this->getMockBuilder($interface)
						   ->disableOriginalConstructor()
						   ->getMock();

		$this->assertFalse($this->template->templateExists($key_2));
		$this->assertFalse($this->template->getTemplate($key_2));
		$this->template->addTemplate($key_2, $template_2);

		$this->assertTrue($this->template->templateExists($key_1));
		$this->assertTrue($this->template->templateExists($key_2));
		$this->assertSame($template_1, $this->template->getTemplate($key_1));
		$this->assertSame($template_2, $this->template->getTemplate($key_2));

		/* removing a template that does not exist ignores operation
		 * and acts as a fluent interface
		 */
		$this->assertFalse($this->template->templateExists('no-key'));
		$this->assertSame(
			$this->template,
			$this->template->removeTemplate('no-key')
		);

		/* removing a template that does exist also returns as a fluent
		 * interface
		 */
		$this->assertSame(
			$this->template,
			$this->template->removeTemplate($key_1)
		);
		$this->assertFalse($this->template->templateExists($key_1));
		$this->assertFalse($this->template->getTemplate($key_1));

		$this->assertSame(
			$this->template,
			$this->template->removeTemplate($key_2)
		);
		$this->assertFalse($this->template->templateExists($key_2));
		$this->assertFalse($this->template->getTemplate($key_2));
	}

	/**
	 * A build item is a value object used when building a template into 
	 * a string and assigning it to another template.
	 *
	 * @return null
	 */
	public function testCreateBuildItem()
	{
		$source = 'my-source';
		$target = 'my-target';
		$label  = 'my-assign-label';
		$item   = $this->template->createBuildItem($source, $target, $label);
		$this->assertInstanceOf(
			'Appfuel\App\View\BuildItem',
			$item
		);
		$this->assertEquals($source, $item->getSource());
		$this->assertEquals($target, $item->getTarget());
		$this->assertEquals($label,  $item->getAssignLabel());
	}

	/**
	 * @return null
	 */
	public function testAddBuildItemGetBuildItems()
	{
		$item_1 = $this->template->createBuildItem(
			'source_1',
			'target_1',
			'assign_label_1'
		);

		$this->assertSame(
			$this->template,
			$this->template->addBuildItem($item_1),
			'must be a fluent interface'
		);

		$expected = array($item_1);
		$this->assertEquals($expected, $this->template->getBuildItems());

		$item_2 = $this->template->createBuildItem(
			'source_2',
			'target_2',
			'assign_label_2'
		);
		$this->template->addBuildItem($item_2);

		$expected = array($item_1, $item_2);
		$this->assertEquals($expected, $this->template->getBuildItems());

		$item_3 = $this->template->createBuildItem(
			'source_3',
			'target_3',
			'assign_label_3'
		);
		$this->template->addBuildItem($item_3);

		$expected = array($item_1, $item_2, $item_3);
		$this->assertEquals($expected, $this->template->getBuildItems());
	}

	/**
	 * Build to allows you to assing one template to build into another 
	 * another template. It will create a build item and push that item 
	 * onto the stack where build will then use it to build the templates
	 *
	 * @return null
	 */
	public function testBuildTo()
	{
		
		$interface	  = 'Appfuel\Framework\App\View\TemplateInterface';
		$sourceKey	  = 'source-key';
		$targetKey    = 'target-key';
		$assignLabel  = 'source-label';

		$this->assertSame(
			$this->template,
			$this->template->buildTo($sourceKey, $assignLabel, $targetKey),
			'must use a fluent interface'
		);
		
		$result = $this->template->getBuildItems();
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey(0, $result);
		$this->assertEquals(1, count($result));

		$buildItem = $result[0];
		$this->assertInstanceOf(
			'Appfuel\Framework\App\View\BuildItemInterface',
			$buildItem
		);
		$this->assertEquals($sourceKey, $buildItem->getSource());
		$this->assertEquals($targetKey, $buildItem->getTarget());
		$this->assertEquals($assignLabel, $buildItem->getAssignLabel());


		$sourceKey2	   = 'source-key2';
		$targetKey2    = 'target-key';
		$assignLabel2  = 'source-label2';

		$this->template->buildTo($sourceKey2, $assignLabel2, $targetKey2);

		$result = $this->template->getBuildItems();
		$this->assertInternalType('array', $result);
		$this->assertEquals(2, count($result));
		$this->assertArrayHasKey(0, $result);
		$this->assertArrayHasKey(1, $result);

		/* make sure we can still get the other build item */
		$buildItem = $result[0];
		$this->assertInstanceOf(
			'Appfuel\Framework\App\View\BuildItemInterface',
			$buildItem
		);
		$this->assertEquals($sourceKey, $buildItem->getSource());
		$this->assertEquals($targetKey, $buildItem->getTarget());
		$this->assertEquals($assignLabel, $buildItem->getAssignLabel());

		/* test the most recently added build item */
		$buildItem = $result[1];
		$this->assertInstanceOf(
			'Appfuel\Framework\App\View\BuildItemInterface',
			$buildItem
		);
		$this->assertEquals($sourceKey2, $buildItem->getSource());
		$this->assertEquals($targetKey2, $buildItem->getTarget());
		$this->assertEquals($assignLabel2, $buildItem->getAssignLabel());
	}

	/**
	 * When no assignment label is given the source key is used as the 
	 * assignment label
	 *
	 * @return null
	 */
	public function testBuildToDefaultAssignLabel()
	{
		$this->assertSame(
			$this->template,
			$this->template->buildTo('my-source', null, 'my-target'),
			'always uses a fluent interface'
		);

		$result = $this->template->getBuildItems();
		$buildItem = $result[0];

		/* prove assignment label is the same as the source */
		$this->assertEquals(
			$buildItem->getSource(), 
			$buildItem->getAssignLabel()
		);
	}

	/**
	 * When no target is specified a special keyword _this_ is used to 
	 * indicate the target is the current template.
	 *
	 * @return null
	 */
	public function testBuildToDefaultTarget()
	{
		$this->assertSame(
			$this->template,
			$this->template->buildTo('my-source', 'my-label'),
			'always uses a fluent interface'
		);

		$result = $this->template->getBuildItems();
		$buildItem = $result[0];

		/* prove assignment label is the same as the source */
		$this->assertEquals(
			'_this_', 
			$buildItem->getTarget()
		);

	}

	/**
	 * The label should be the same as source and the target sould be 
	 * the keyword _this_
	 *
	 * @return null
	 */
	public function testBuildToDefaultAssignLabelAndTarget()
	{
		$this->assertSame(
			$this->template,
			$this->template->buildTo('my-source'),
			'always uses a fluent interface'
		);

		$result = $this->template->getBuildItems();
		$buildItem = $result[0];

		/* prove assignment label is the same as the source */
		$this->assertEquals(
			'_this_', 
			$buildItem->getTarget()
		);

		$this->assertEquals(
			$buildItem->getSource(),
			$buildItem->getAssignLabel(),
			'assignment label must be the same as the source key'
		);
	}

	/**
	 * AssignTo allows you to assign a name/value pair into any template that
	 * you are holding
	 *
	 * @return null
	 */
	public function testAssignTo()
	{
		$templateA = new Template('templateA');
		$templateB = new Template('templateB');

		$this->template->addTemplate('templateA', $templateA)
					   ->addTemplate('templateB', $templateB);

		$this->assertSame(
			$this->template,
			$this->template->assignTo('templateA', 'foo', 'bar'),
			'must use a fluent interface'
		);

		/* prove only templateA recieves the assignment for foo=>bar */
		$this->assertEquals('bar', $templateA->get('foo'));
		$this->assertNull($templateB->get('foo'));
		$this->assertNull($this->template->get('foo'));

		$this->assertSame(
			$this->template,
			$this->template->assignTo('templateB', 'baz', 'biz'),
			'must use a fluent interface'
		);
		$this->assertEquals('biz', $templateB->get('baz'));
		$this->assertNull($templateA->get('baz'));
		$this->assertNull($this->template->get('baz'));

		/* when the template does not exist the request is ignored */
		$this->assertSame(
			$this->template,
			$this->template->assignTo('no-template', 'no-label', 'no-value'),
			'should still be a fluent interface'
		);
	
		$this->assertNull($this->template->get('no-label'));
		$this->assertNull($templateA->get('no-label'));
		$this->assertNull($templateB->get('no-label'));
	}
}
