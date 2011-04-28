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
namespace Test\Appfuel\App;

use Test\AfTestCase as ParentTestCase,
	Appfuel\App\File,
	SplFileInfo,
	Appfuel\Stdlib\Filesystem\Manager as FileManager;

/**
 * The Appfuel\App\File extends SplFileInfo for two reason. Firstly, to give
 * the file the ability to report back the applications base path. Generally
 * this file will only be used to reference file in the application. Secondly,
 * when the file does not exist, SplFileInfo::getRealPath returns an empty 
 * string but we generally need that string, so we supply getFullPath which
 * reports the absolute path even if it does not exist.
 */
class FileTest extends ParentTestCase
{
	/**
	 * Test the file is an SplFileInfo, Appfuel\App\File and getResource
	 * returns the relative path from the resource directory.
	 *
	 * @return null
	 */
	public function test_fileExists()
	{
		$path  = 'test' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;
		$path .= FileManager::classNameToDir(get_class($this));
		$path .= DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
		$path .= 'app_sample_file.txt';

		$file = new File($path);
		$this->assertInstanceOf(
			'Appfuel\App\File',
			$file
		);
		$this->assertInstanceOf(
			'SplFileInfo',
			$file
		);


		//$expected = 'resource' . DIRECTORY_SEPARATOR . $path;
		//$this->assertEquals($expected, $file->getFullPath());
		
	}
}
