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
namespace TestFuel\Unit\Kernel;

use Appfuel\Kernel\IncludePath,
	TestFuel\TestCase\BaseTestCase;

/**
 * Test the ability to change the php include path. These changes include 
 * appending to the path, prepending to the path and replacing the path. 
 */
class IncludePathTest extends BaseTestCase
{
	/**
	 * @return	null
	 */
	public function testInitialState()
	{	
		$path = new IncludePath();
		$this->assertInstanceOf('Appfuel\Kernel\IncludePathInterface',$path);
		return $path;	
	}

	/**
     * The method usePaths excepts two paramters the second one indicates the
     * action to perform on the include path. Do you append, prepend or 
     * replace the path. The default is to replace and thats what we are 
     * testing here.
     *
	 * @depends	testInitialState
	 * @return null
	 */
	public function testSetPathsDefault(IncludePath $path)
	{
		$paths = array(
			'path_1',
			'path_2'
		);

		$oldPath	 = get_include_path();
		$expected    = 'path_1' . PATH_SEPARATOR . 'path_2';
		$result      = $path->setPath($paths);
		$includePath = get_include_path();
		$this->restoreIncludePath();

		$this->assertEquals($result, $oldPath);
		$this->assertEquals($expected, $includePath);
	}

	/**
	 * @depends	testInitialState
	 * @return null
	 */
	public function testSetPathAppend(IncludePath $path)
	{
		$paths = array(
			'path_1',
			'path_2'
		);

		$oldPath	 = get_include_path();
		$expected    = $oldPath . PATH_SEPARATOR . 
					   'path_1' . PATH_SEPARATOR . 'path_2';
		$result      = $path->setPath($paths, 'append');
		$includePath = get_include_path();
		$this->restoreIncludePath();

		$this->assertEquals($result, $oldPath);
		$this->assertEquals($expected, $includePath);
	}

	/**
	 * @depends	testInitialState
	 * @return null
	 */
	public function testSetPathPrepend(IncludePath $path)
	{
		$paths = array(
			'path_1',
			'path_2'
		);

		$oldPath	 = get_include_path();
		$expected    = 'path_1' . PATH_SEPARATOR . 
					   'path_2' . PATH_SEPARATOR .
					   $oldPath;

		$result      = $path->setPath($paths, 'prepend');
		$includePath = get_include_path();
		$this->restoreIncludePath();
		
		$this->assertEquals($result, $oldPath);
		$this->assertEquals($expected, $includePath);
	}

	/**
	 * @depends	testInitialState
	 * @return null
	 */
	public function testSetPathReplace(IncludePath $path)
	{
		$paths	  = array('path_1','path_2');
		$oldPath  = get_include_path();
		$expected = 'path_1' . PATH_SEPARATOR . 'path_2';

		$result      = $path->setPath($paths, 'replace');
		$includePath = get_include_path();
		
		$this->restoreIncludePath();
		$this->assertEquals($result, $oldPath);
		$this->assertEquals($expected, $includePath);
	}
}
