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
	Appfuel\App\View\ViewManager,
	StdClass;

/**
 * The series of tests covers testing the response type functionality such as
 * checking valid response types adding remove getting and setting valid
 * reponseTypes. Setting and getting the view response. Making assignments
 * to the view response
 */
class ViewManagerTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Template
	 */
	protected $viewManager = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->viewManager = new ViewManager();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->viewManager);
	}

	/**
	 * @return	null
	 */
	public function testGetSetView()
	{
		$this->assertNull($this->viewManager->getView());
		$view = $this->getMock('Appfuel\Framework\App\View\ViewInterface');
		$this->assertSame(
			$this->viewManager,
			$this->viewManager->setView($view),
			'must use fluent interface'
		);
		$this->assertSame($view, $this->viewManager->getView());
	}
}
