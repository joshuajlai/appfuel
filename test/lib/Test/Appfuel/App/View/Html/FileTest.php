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
namespace Test\Appfuel\App\View\Html;

use Test\AfTestCase as ParentTestCase,
	Appfuel\App\View\Html\File as File;

/**
 * The html file simply extends the relative path to start at html instead of
 * the namespace.
 */
class FileTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var File
	 */
	protected $file = null;

	/**
	 * Relative path used in the constructor 
	 * @var string
	 */
	protected $relativePath = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		/* 
		 * we don't have to be os specific cause we will not actually use
		 * this path
		 */
		$this->relativePath = 'some/relative/path';
		$this->file = new File($this->relativePath);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->file);
	}

	/**
	 * @return null
	 */
	public function testGetClientsidePath()
	{
		/* we need to care about the directory separator for the prefix */
		$expected = 'clientside' . DIRECTORY_SEPARATOR . 
					'appfuel'    . DIRECTORY_SEPARATOR .
					'html'       . DIRECTORY_SEPARATOR . 
					$this->relativePath;

		$this->assertEquals($expected, $this->file->getClientsidePath());
	}
}
