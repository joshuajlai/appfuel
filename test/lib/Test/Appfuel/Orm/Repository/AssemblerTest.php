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
	Appfuel\Orm\Domain\ObjectFactory,
	Appfuel\Orm\Domain\DataBuilder,
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
	 * @var DataBuilderInterface
	 */
	protected $dataBuilder = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->backupRegistry();

		$this->dataBuilder   = new DataBuilder(new ObjectFactory());
		$this->dbHandler     = new DbHandler();
		$this->sourceHandler = new SourceHandler($this->dbHandler);
		
		$this->asm = new Assembler(
			$this->sourceHandler,
			$this->dataBuilder
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->restoreRegistry();
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

	/**
	 * @return null
	 */
	public function testGetSourceHandler()
	{
		$this->assertSame(
			$this->sourceHandler,
			$this->asm->getSourceHandler(),
			'should be the same source handler passed into the constructor'
		);

		$this->assertSame(
			$this->dataBuilder,
			$this->asm->getDataBuilder(),
			'should be the same data builder passed into the constructor'
		);
	}

	/**
	 * This test represents the general use case where the repository
	 * create a criteria used to describe what to build and the source
	 * handler has alreay return the pre mapped data to be built into
	 * the domains
	 *
	 * @return	null
	 */
	public function testBuildDataDefaultBuildMethod()
	{
		/* declare where the fake domain will be found */
		$map = array('user' => __NAMESPACE__ . '\Assembler\User');
		$this->initializeRegistry(array('domain-keys' => $map));
		$userClass = $map['user'] . '\UserModel';

		/*
		 * The represents the premapped data that would come back from the
		 * data source
		 */
		$data = array(
			'id'		=> 101,
			'firstName' => 'Robert',
			'lastName'  => 'Scott-Buccluech',
			'email'		=> 'rsb.code@gmail.com' 
		);
		
		$criteria = new Criteria();
		$criteria->add('domain-key', 'user');
		$user = $this->asm->buildData($criteria, $data);
		$this->assertInstanceOf($userClass, $user);
	
		$state = $user->_getDomainState();
		$this->assertTrue($state->isMarshal());
		$this->assertEquals($data['id'], $user->getId());
		$this->assertEquals($data['firstName'], $user->getFirstName());
		$this->assertEquals($data['lastName'], $user->getLastName());
		$this->assertEquals($data['email'], $user->getEmail());	
	}
}
