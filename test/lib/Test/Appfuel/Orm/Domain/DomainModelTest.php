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
namespace Test\Appfuel\Db\Adapter;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Orm\Domain\DomainState,
	Appfuel\Orm\Domain\DomainModel;

/**
 * I made this manual mock class because many methods that exist in the 
 * abstract class are intended to work on member variables that only exist
 * in the concrete class an phpunit does not account for this (or at least 
 * i am unware of it).
 */
class MockDomainModel extends DomainModel
{
	protected $memberA = null;
	protected $memberB = null;
	protected $memberC = null;
	protected $memberD = null;

	public function getMemberA()
	{
		return $this->memberA;
	}

	public function setMemberA($value)
	{
		$this->memberA = $value;
		return $this;
	}

	public function getMemberB()
	{
		return $this->memberB;
	}

	public function setMemberB($value)
	{
		$this->memberB = $value;
		return $this;
	}

	public function getMemberC()
	{
		return $this->memberC;
	}

	public function setMemberC($value)
	{
		$this->memberC = $value;
		return $this;
	}

	public function getMemberD()
	{
		return $this->memberD;
	}

	public function setMemberD(StdClass $value)
	{
		$this->memberD = $value;
		return $this;
	}
}

/**
 */
class DomainModelTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var DomainModel
	 */
	protected $domain = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->domain = new MockDomainModel();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->domain);
	}

	public function testDomainInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainModelInterface',
			$this->domain
		);
	}

	/**
	 * @return null
	 */
	public function testGetSet()
	{
		$this->assertNull($this->domain->getDomainState());
	
		$stateInterface = 'Appfuel\Framework\Orm\Domain\DomainStateInterface';
		$state = $this->getMock($stateInterface);
		$this->assertSame($this->domain, $this->domain->setDomainState($state));
		$this->assertSame($state, $this->domain->getDomainState());
	}

	/**
	 * @return null
	 */
	public function testIsStrictMarshal()
	{
		/* default value is false */
		$this->assertFalse($this->domain->isStrictMarshalling());
		$this->assertSame(
			$this->domain, 
			$this->domain->enableStrictMarshalling()
		);

		$this->assertTrue($this->domain->isStrictMarshalling());

		$this->assertSame(
			$this->domain, 
			$this->domain->disableStrictMarshalling()
		);
		$this->assertFalse($this->domain->isStrictMarshalling());
	}

	/**
	 * When used with no paremeters markMarshal will change domain state
	 * if the domain state object does not exist it will be created 
	 * automatically
	 *
	 * @return null
	 */
	public function testMarkMarshalDefaultNoMembers()
	{
		$this->assertNull($this->domain->getDomainState());
		$this->assertSame($this->domain, $this->domain->marshal());

		$state = $this->domain->getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainStateInterface',
			$state
		);

		$this->assertTrue($state->isMarshal());
	}

	/**
	 * @return null
	 */
	public function testMarkMarshalNotStrictValidMembers()
	{
		$data = array(
			'memberA' => 'value_a',
			'memberB' => 12345,
			'memberC' => array(1,2,3,4)
		);
		$this->assertNull($this->domain->getDomainState());
		$this->assertFalse($this->domain->isStrictMarshalling());
		$this->assertSame(
			$this->domain,
			$this->domain->marshal($data)
		);
		$state = $this->domain->getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainStateInterface',
			$state
		);
		$this->assertTrue($state->isMarshal());
		$this->assertEquals($data, $state->getInitialMembers());
	}

	/**
	 * @return null
	 */
	public function testMarkMarshalNotStrictMemberMissing()
	{
		$data = array(
			'memberA' => 'value_a',
			'memberC' => array(1,2,3,4)
		);
		$this->assertNull($this->domain->getDomainState());
		$this->assertFalse($this->domain->isStrictMarshalling());
		$this->assertSame(
			$this->domain,
			$this->domain->marshal($data)
		);
		$state = $this->domain->getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainStateInterface',
			$state
		);
		$this->assertTrue($state->isMarshal());
		$this->assertEquals($data, $state->getInitialMembers());
	}

	/**
	 * @expectedException	BadMethodCallException
	 * @return null
	 */
	public function testMarkMarshalNotStrictBadMember()
	{
		$data = array(
			'memberA' => 'abc',
			'memberC' => array(1,2,3,4),
			'memberD' => 'asdasd'
		);
		$this->assertNull($this->domain->getDomainState());
		$this->assertFalse($this->domain->isStrictMarshalling());
		$this->assertSame(
			$this->domain,
			$this->domain->marshal($data)
		);
		$state = $this->domain->getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainStateInterface',
			$state
		);
		$this->assertTrue($state->isMarshal());
		$this->assertEquals($data, $state->getInitialMembers());
	}

	/**
	 * @return null
	 */
	public function testMarkMarshalStrictValidMembers()
	{
		$data = array(
			'memberA' => 'value_a',
			'memberB' => 12345,
			'memberC' => array(1,2,3,4),
			'memberD' => new StdClass()
		);
		$this->domain->enableStrictMarshalling();

		$this->assertNull($this->domain->getDomainState());
		$this->assertTrue($this->domain->isStrictMarshalling());
		$this->assertSame(
			$this->domain,
			$this->domain->marshal($data)
		);
		$state = $this->domain->getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainStateInterface',
			$state
		);
		$this->assertTrue($state->isMarshal());
		$this->assertEquals($data, $state->getInitialMembers());
	}

	/**
	 * @expectedException	BadMethodCallException
	 * @return null
	 */
	public function testMarkMarshalStrictMemberDoesNotExist()
	{
		$data = array(
			'memberA' => 'value_a',
			'memberABC' => 12345,
			'memberC' => array(1,2,3,4),
			'memberD' => new StdClass()
		);
		$this->domain->enableStrictMarshalling();

		$this->assertNull($this->domain->getDomainState());
		$this->assertTrue($this->domain->isStrictMarshalling());
			
		$this->domain->marshal($data);
	}


	/**
	 * Test shows that if the state object already exists it won't be created
	 *
	 * @return null
	 */
	public function testMarkMarshalStateAlreadyExists()
	{
		$state = new DomainState();
		$this->domain->setDomainState($state);

		$data = array(
			'memberA' => 'value_a',
			'memberB' => 12345,
			'memberC' => array(1,2,3,4),
			'memberD' => new StdClass()
		);
		$this->domain->enableStrictMarshalling();

		$this->assertTrue($this->domain->isStrictMarshalling());
		$this->assertSame(
			$this->domain,
			$this->domain->marshal($data)
		);
		$returnedState = $this->domain->getDomainState();
		$this->assertSame($state, $returnedState);
		$this->assertTrue($state->isMarshal());
		$this->assertEquals($data, $state->getInitialMembers());
	}

	/**
	 * Test that we can mark all the members of the mock domain dirty and that
	 * duplicates are ignored
	 *
	 * @return null
	 */
	public function testMarkDirtyValidMembers()
	{
		/* prove no state */
		$this->assertNull($this->domain->getDomainState());
		$state = new DomainState();

		/* inject the domain state */
		$this->domain->setDomainState($state);
		$this->assertSame(
			$this->domain,
			$this->domain->markDirty('memberA')
		);

		$this->assertTrue($state->isDirty());
		$this->assertFalse($state->isNew());
		$this->assertFalse($state->isDelete());
		$this->assertFalse($state->isMarshal());
		$this->assertTrue($state->isDirtyMember('memberA'));
		$this->assertEquals(array('memberA'), $state->getDirtyMembers());

		$this->assertSame(
			$this->domain,
			$this->domain->markDirty('memberB')
		);

		$this->assertTrue($state->isDirty());
		$this->assertTrue($state->isDirtyMember('memberA'));
		$this->assertTrue($state->isDirtyMember('memberB'));
		$this->assertEquals(
			array('memberA', 'memberB'), 
			$state->getDirtyMembers()
		);

		$this->assertSame(
			$this->domain,
			$this->domain->markDirty('memberC')
		);

		$this->assertTrue($state->isDirty());
		$this->assertTrue($state->isDirtyMember('memberA'));
		$this->assertTrue($state->isDirtyMember('memberB'));
		$this->assertTrue($state->isDirtyMember('memberC'));
		$this->assertEquals(
			array('memberA', 'memberB', 'memberC'), 
			$state->getDirtyMembers()
		);

		$this->assertSame(
			$this->domain,
			$this->domain->markDirty('memberD')
		);

		$this->assertTrue($state->isDirty());
		$this->assertTrue($state->isDirtyMember('memberA'));
		$this->assertTrue($state->isDirtyMember('memberB'));
		$this->assertTrue($state->isDirtyMember('memberC'));
		$this->assertTrue($state->isDirtyMember('memberD'));
		$this->assertEquals(
			array('memberA', 'memberB', 'memberC', 'memberD'), 
			$state->getDirtyMembers()
		);

		/* test for duplicates */
		$this->domain->markDirty('memberA');
		$this->domain->markDirty('memberB');
		$this->domain->markDirty('memberC');
		$this->domain->markDirty('memberD');
		$this->assertEquals(
			array('memberA', 'memberB', 'memberC', 'memberD'), 
			$state->getDirtyMembers()
		);	
	}

	/**
	 * @return null
	 */
	public function testMarkDirtyNoStateInjected()
	{
		$this->assertNull($this->domain->getDomainState());
		$this->assertSame(
			$this->domain,	
			$this->domain->markDirty('memberB')
		);
		$state = $this->domain->getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainStateInterface',
			$state
		);
		$this->assertTrue($state->isDirty());
		$this->assertFalse($state->isNew());
		$this->assertFalse($state->isDelete());
		$this->assertFalse($state->isMarshal());
		$this->assertTrue($state->isDirtyMember('memberB'));
		$this->assertEquals(array('memberB'), $state->getDirtyMembers());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testMarkDirtyMemberDoesNotExist()
	{
		$this->domain->markDirty('this-member-does-not-exist');
	}

	/**
	 * @return null
	 */
	public function testMarkNewStateInjected()
	{
		$state = new DomainState();
		$state->setState('marshal');
		$this->domain->setDomainState($state);

		$this->assertSame(
			$this->domain,
			$this->domain->markNew()
		);
		$this->assertTrue($state->isNew());
	}

	/**
	 * @return null
	 */
	public function testMarkNew()
	{
		$this->assertSame(
			$this->domain,
			$this->domain->markNew()
		);

		$state = $this->domain->getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainStateInterface',
			$state
		);
		$this->assertTrue($state->isNew());
	}

	/**
	 * @return null
	 */
	public function testMarkDeleteStateInjected()
	{
		$state = new DomainState();
		$this->domain->setDomainState($state);

		$this->assertSame(
			$this->domain,
			$this->domain->markDelete()
		);
		$this->assertTrue($state->isDelete());
	}

	/**
	 * @return null
	 */
	public function testMarkDelete()
	{
		$this->assertSame(
			$this->domain,
			$this->domain->markDelete()
		);

		$state = $this->domain->getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainStateInterface',
			$state
		);
		$this->assertTrue($state->isDelete());
	}
}
