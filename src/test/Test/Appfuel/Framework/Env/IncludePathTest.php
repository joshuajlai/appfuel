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
namespace Test\Appfuel\Framework\Env;

use Test\AfTestCase					  as ParentTestCase,
	Appfuel\Stdlib\Filesystem\Manager as FileManager,
	Appfuel\Registry,
	Appfuel\Framework\Env\IncludePath;

/**
 * Test the ability to change the php include path. These changes include 
 * appending to the path, prepending to the path and replacing the path. 
 */
class IncludePathTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var	IncludePath
	 */
	protected $includePath = NULL;

	/**
	 * Save the include path and registry settings
	 * @return null
	 */
	public function setUp()
	{
		$this->backupIncludePath();
		$this->includePath = new IncludePath();
	}

	/**
	 * Restore the include path and registry settings
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->includePath);
		$this->restoreIncludePath();
		$this->restoreAppfuelSettings();
	}

	/**
     * The method usePaths excepts two paramters the second one indicates the
     * action to perform on the include path. Do you append, prepend or 
     * replace the path. The default is to replace and thats what we are 
     * testing here.
     *
	 * @return null
	 */
	public function testUsePathsDefault()
	{
		$paths = array(
			'path_1',
			'path_2'
		);

		$oldPath	 = get_include_path();
		$expected    = 'path_1' . PATH_SEPARATOR . 'path_2';
		$result      = $this->includePath->usePaths($paths);
		$includePath = get_include_path();
		$this->restoreIncludePath();

		$this->assertEquals($result, $oldPath);
		$this->assertEquals($expected, $includePath);
	}

	/**
	 * @return null
	 */
	public function testUsePathsAppend()
	{
		$paths = array(
			'path_1',
			'path_2'
		);

		$oldPath	 = get_include_path();
		$expected    = $oldPath . PATH_SEPARATOR . 
					   'path_1' . PATH_SEPARATOR . 'path_2';
		$result      = $this->includePath->usePaths($paths, 'append');
		$includePath = get_include_path();
		$this->restoreIncludePath();

		$this->assertEquals($result, $oldPath);
		$this->assertEquals($expected, $includePath);
	}

	/**
	 * @return null
	 */
	public function testUsePathsPrepend()
	{
		$paths = array(
			'path_1',
			'path_2'
		);

		$oldPath	 = get_include_path();
		$expected    = 'path_1' . PATH_SEPARATOR . 
					   'path_2' . PATH_SEPARATOR .
					   $oldPath;

		$result      = $this->includePath->usePaths($paths, 'prepend');
		$includePath = get_include_path();
		$this->restoreIncludePath();
		
		$this->assertEquals($result, $oldPath);
		$this->assertEquals($expected, $includePath);
	}

	/**
	 * Same as default
	 * @return null
	 */
	public function testUsePathsReplace()
	{
		$paths = array(
			'path_1',
			'path_2'
		);
		$oldPath	 = get_include_path();
		$expected    = 'path_1' . PATH_SEPARATOR . 
					   'path_2';

		$result      = $this->includePath->usePaths($paths, 'replace');
		$includePath = get_include_path();
		
		$this->restoreIncludePath();
		$this->assertEquals($result, $oldPath);
		$this->assertEquals($expected, $includePath);
	}
}

