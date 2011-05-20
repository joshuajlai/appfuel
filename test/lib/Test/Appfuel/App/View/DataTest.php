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
	Appfuel\App\View\Data;

/**
 * The data class is used solely to hold the data needed by the template. At
 * this point it extends Appfuel\Stdlib\Data\Dictionary and adds an alias
 * assign to the method add. We will do a light test to ensure that alias is
 * working
 */
class DataTest extends ParentTestCase
{
	/**
	 * @return null
	 */
	public function testConstructorAssign()
	{
		$data = new Data();	
		$this->assertInstanceOf(
			'Appfuel\Data\Dictionary',
			$data
		);

		$data->assign('foo', 'bar');
		$this->assertTrue($data->exists('foo'));
		$this->assertEquals('bar', $data->get('foo'));
	}
}
