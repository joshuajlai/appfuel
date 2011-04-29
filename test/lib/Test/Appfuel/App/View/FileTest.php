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
	Appfuel\App\View\File;

/**
 * The view file is an application file that knows where the resource
 * directory is.
 */
class FileTest extends ParentTestCase
{
	/**
	 * Test the file is an SplFileInfo, Appfuel\App\File and getResource
	 * returns the relative path from the resource directory.
	 *
	 * @return null
	 */
	public function testConstructorGetClientsidePathDefault()
	{
		$path = 'some/relative/path';
		$file = new File($path);
		$this->assertInstanceOf(
			'Appfuel\App\File',
			$file
		);

		/* test returning relative */
		$expected = 'clientside' . DIRECTORY_SEPARATOR . 
					'appfuel'    . DIRECTORY_SEPARATOR .
					$path;
		$this->assertEquals($expected, $file->getClientsidePath());
	
		/* test returning absolute */
		$expected = $file->getBasePath() . DIRECTORY_SEPARATOR .
					$expected;	
		$this->assertEquals($expected, $file->getClientsidePath(true));
	}

	/**
	 * Test the file constructur by using a different namespace
	 *
	 * @return null
	 */
	public function testConstructorGetClientsidePathNamespace()
	{
		$path = 'some/relative/path';
		$namespace = 'my-app-name';
		$file = new File($path, $namespace);
		$this->assertInstanceOf(
			'Appfuel\App\File',
			$file
		);

		/* test returning relative */
		$expected = 'clientside' . DIRECTORY_SEPARATOR . 
					$namespace   . DIRECTORY_SEPARATOR .
					$path;
		$this->assertEquals($expected, $file->getClientsidePath());
	
		/* test returning absolute */
		$expected = $file->getBasePath() . DIRECTORY_SEPARATOR .
					$expected;	
		$this->assertEquals($expected, $file->getClientsidePath(true));
	}

	/**
	 * Test the file constructur by using an empty namespace which will
	 * not use a sub directory in the clientside directory
	 *
	 * @return null
	 */
	public function testConstructorGetClientsidePathEmptyNamespace()
	{
		$path = 'some/relative/path';
		$namespace = '';
		$file = new File($path, $namespace);
		$this->assertInstanceOf(
			'Appfuel\App\File',
			$file
		);

		/* test returning relative */
		$expected = 'clientside' . DIRECTORY_SEPARATOR . 
					$path;
		$this->assertEquals($expected, $file->getClientsidePath());
	
		/* test returning absolute */
		$expected = $file->getBasePath() . DIRECTORY_SEPARATOR .
					$expected;	
		$this->assertEquals($expected, $file->getClientsidePath(true));
	}
}
