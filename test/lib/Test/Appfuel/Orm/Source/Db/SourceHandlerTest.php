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
namespace Test\Appfuel\Orm\DataSource;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Handler\DbHandler,
	Appfuel\Orm\Source\Db\SourceHandler;

/**
 * The Database source handler builds the sql and the database request and
 * sends the request to the database handing back a valid database response
 */
class SourceHandlerTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var SourceHandler
	 */
	protected $sourceHandler = null;

	/**
	 * Db Handler
	 * @var DbHandler
	 */
	protected $dbHandler = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->dbHandler     = new DbHandler();
		$this->sourceHandler = new SourceHandler($this->dbHandler);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->dbHandler);
		unset($this->sourceHandler);
	}

	/**
	 * @return null
	 */
	public function testImplementedInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Source\SourceHandlerInterface',
			$this->sourceHandler
		);
	}
}
