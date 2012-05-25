<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Testfuel\Unit\Validate;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Validate\ValidationManager;

class ValidationManagerTest extends BaseTestCase
{
	/**
	 * @var array
	 */
	protected $validatorMapBk = array();

	/**
	 * @var array
	 */
	protected $filterMapBk = array();


	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->validatorMapBk = ValidationManager::getValidatorMap();
		$this->filterMapBk = ValidationManager::getFilterMap();
		ValidationManager::clear();
	}

	public function tearDown()
	{
		ValidationManager::clear();
		ValidationManager::setValidatorMap($this->validatorMapBk);
		ValidationManager::setFilterMap($this->filterMapBk);
	}

	public function testOne()
	{
		$this->assertTrue(true);
	}
}
