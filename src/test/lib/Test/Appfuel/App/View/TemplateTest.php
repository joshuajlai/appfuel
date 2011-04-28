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
	Appfuel\App\View\Template;

/**
 * The view template is an extension of view data that adds on the ability 
 * to have template files the may or may not get the data in the templates
 * dictionary.
 */
class TemplateTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Template
	 */
	protected $template = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->template = new Template();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->template);
	}

	/**
	 * Make sure that template extends from view data
	 *
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'Appfuel\App\View\Data',
			$this->template,
			'Template must be a view data object'
		);
	}	
}
