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
}
