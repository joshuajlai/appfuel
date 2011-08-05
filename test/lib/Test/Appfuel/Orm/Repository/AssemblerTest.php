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
namespace Test\Appfuel\Orm\Repository;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Handler\DbHandler,
	Appfuel\Orm\Domain\DomainExpr,
	Appfuel\Orm\Repository\Criteria,
	Appfuel\Orm\Repository\Assembler,
	Appfuel\Orm\Source\Db\SourceHandler,
	Appfuel\Framework\DataStructure\Dictionary;

/**
 */
class AssemblerTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Assembler
	 */
	protected $asm = null;

	/**
	 * Handle interacting with the datasource
	 * @var	SourceHandlerInterface
	 */	
	protected $sourceHandler = null;

	/**
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
		$this->asm			 = new Assembler($this->sourceHandler);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->dbHandler);
		unset($this->sourceHandler);
		unset($this->asm);
	}

	/**
	 * @return null
	 */
	public function testImplementedInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Repository\AssemblerInterface',
			$this->asm
		);
	}

}
