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
namespace TestFuel\Test\Domain\Operation;

use TestFuel\TestCase\BaseTestCase,
	Appfuel\Domain\Operation\OpRouteList,
	Appfuel\Framework\Action\ActionControllerDetail;

/**
 * The operation list to prevent the framework from having to access the 
 * database to get a single operation. Since we know all the operations before
 * we deploy the application we can build those into a php file. This class
 * was ment to use the array of data in that phpfile.
 */
class OperationListTest extends BaseTestCase
{
	/**
	 * @return null
	 */
	public function testGetFileData()
	{
	}
}
