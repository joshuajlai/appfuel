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
	Appfuel\App\View\Builder;

/**
 * 
 */
class BuilderTest extends ParentTestCase
{
	/**
	 * System under test
	 * @return	Builder
	 */ 	
	protected $builder = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->builder = new Builder();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->builder);
	}

	/**
	 * Supported documents include html, json, cli, csv, null
	 * @return	null
	 */ 
	public function testCreateDocValid()
	{
		$this->assertInstanceOf(
			'\Appfuel\App\View\Html\Document',
			$this->builder->createDoc('html'),
			'must create and html document'
		);

		$this->assertInstanceOf(
			'\Appfuel\App\View\Json\Document',
			$this->builder->createDoc('json'),
			'must create and json document'
		);

		$this->assertInstanceOf(
			'\Appfuel\App\View\Cli\Document',
			$this->builder->createDoc('cli'),
			'must create and cli document'
		);

		$this->assertInstanceOf(
			'\Appfuel\App\View\Csv\Document',
			$this->builder->createDoc('csv'),
			'must create and csv document'
		);

		$this->assertInstanceOf(
			'\Appfuel\App\View\Null\Document',
			$this->builder->createDoc('null'),
			'must create and null document'
		);

		/* parameter can all lower or proper case */
		$this->assertInstanceOf(
			'\Appfuel\App\View\Html\Document',
			$this->builder->createDoc('Html'),
			'must create and html document'
		);

		/* can not be upper case */
		$this->assertFalse($this->builder->createDoc('JSON'));

		/* can not be mixed case */
		$this->assertFalse($this->builder->createDoc('JsoN'));
	}

}
