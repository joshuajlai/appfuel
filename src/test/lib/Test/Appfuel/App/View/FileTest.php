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
	public function testConstructorGetResourcePath()
	{
		$path = 'some/relative/path';
		$file = new File($path);
		$this->assertInstanceOf(
			'Appfuel\App\File',
			$file
		);

		$expected = 'resource' . DIRECTORY_SEPARATOR . $path;
		$this->assertEquals($expected, $file->getResourcePath());
		
	}
}
