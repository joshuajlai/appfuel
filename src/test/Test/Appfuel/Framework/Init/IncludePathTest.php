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
namespace Test\Appfuel\Framework\Init;

use Test\AfTestCase					  as ParentTestCase,
	Appfuel\Stdlib\Filesystem\Manager as FileManager,
	Appfuel\Registry,
	Appfuel\Framework\Init\IncludePath;

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

		echo "\n", print_r('calling use path for prepend ---',1), "\n"; 
		$result      = $this->usePaths($paths, 'prepend');
		echo "\n", print_r('call finished for prenpend test ----' ,1), "\n";exit;
		$includePath = get_include_path();
		$this->restoreIncludePath();
		
		$this->assertEquals($result, $oldPath);
		$this->assertEquals($expected, $includePath);
	}

	/**
	 * Same as default
	 * @return null
	 */
	public function ztestUsePathsReplace()
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

