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
namespace Test;

/**
 * 
 */
class AfTestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * Absolute path to the testing directory
	 */
	protected $testPath = NULL;

	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = AF_BASE_PATH;

	/**
	 * @return null
	 */
	public function getBasePath()
	{
		return $this->basePath;
	}

	/**
	 * @return null
	 */
	public function getTestBase()
	{
		return $this->basePath . DIRECTORY_SEPARATOR . 'test';
	}


}

