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
namespace TestFuel\Unit\Orm\Domain;

use TestFuel\TestCase\BaseTestCase,
	Appfuel\Orm\Domain\DomainState;

/**
 * Test the ability of the domain state to handle changes in the domain's 
 * state
 */
class DomainStateTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var DomainState
	 */
	protected $state = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->state = new DomainState();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->state);
	}

	/**
	 * @return null
	 */
	public function testImplementedInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Orm\Domain\DomainStateInterface',
			$this->state
		);
	}

	/**
	 * There are four states and we will test to make sure we can get and
	 * set all four
	 *
	 * @return null
	 */
	public function testGetDefaultState()
	{
		$marshal= 'marshal';
		$new	= 'new';
		$dirty  = 'dirty';
		$delete = 'delete';
		
		$this->assertEquals($new, $this->state->getState());
		$this->assertTrue($this->state->isNew());
		$this->assertFalse($this->state->isMarshal());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isDelete());

		$this->assertSame($this->state, $this->state->setState($marshal));
		$this->assertEquals($marshal, $this->state->getState());
		$this->assertTrue($this->state->isMarshal());
		$this->assertFalse($this->state->isNew());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isDelete());

		$this->assertSame($this->state, $this->state->setState($dirty));
		$this->assertEquals($dirty, $this->state->getState());
		/*
		 * isDirty is false because we have to add dirty members with
		 * markDirty
		 */
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isMarshal());
		$this->assertFalse($this->state->isNew());
		$this->assertFalse($this->state->isDelete());

		$this->assertSame($this->state, $this->state->setState($delete));
		$this->assertEquals($delete, $this->state->getState());
		$this->assertTrue($this->state->isDelete());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isMarshal());
		$this->assertFalse($this->state->isNew());

		$this->assertSame($this->state, $this->state->setState($new));
		$this->assertEquals($new, $this->state->getState());
		$this->assertTrue($this->state->isNew());
		$this->assertFalse($this->state->isDelete());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isMarshal());
	}

	/**
	 * @return null
	 */
	public function testMarkMarshalGetInitialMembersEmptyArray()
	{
		/* prove not in a state of marshal */
		$this->assertFalse($this->state->isMarshal());
		$this->assertSame($this->state, $this->state->markMarshal());
		$this->assertTrue($this->state->isMarshal());
		$this->assertEquals(array(), $this->state->getInitialMembers());
	}
	
	/**
	 * @return null
	 */
	public function testMarkMarshalGetInitialMembers()
	{
		/* prove not in a state of marshal */
		$this->assertFalse($this->state->isMarshal());

		$members = array(
			'userId'	=> 1,
			'userName'	=> 'Robert'
		);
		$this->assertSame($this->state, $this->state->markMarshal($members));
		$this->assertTrue($this->state->isMarshal());
		$this->assertEquals($members, $this->state->getInitialMembers());
	}

	/**
	 * @return null
	 */
	public function testMarkDiryGetDirtyMembersIsDiryMembers()
	{
		/* prove not dirty */
		$this->assertFalse($this->state->isDirty());
		$this->assertEquals(array(), $this->state->getDirtyMembers());
		
		$dirty = array(
			'member_1',
			'member_2',
			'member_3'
		);
		
		$this->assertFalse($this->state->isDirtyMember($dirty[0]));
		$this->assertSame($this->state, $this->state->markDirty($dirty[0]));
		$this->assertTrue($this->state->isDirtyMember($dirty[0]));
		
		/* 
		 * the state will only turn dirty when there are dirty members listed 
		 */
		$this->assertTrue($this->state->isDirty());
		$this->assertEquals(
			array($dirty[0]),
			$this->state->getDirtyMembers()
		);


		$this->assertFalse($this->state->isDirtyMember($dirty[1]));
		$this->assertSame($this->state, $this->state->markDirty($dirty[1]));
		$this->assertTrue($this->state->isDirtyMember($dirty[1]));
		
		/* 
		 * the state will only turn dirty when there are dirty members listed 
		 */
		$this->assertTrue($this->state->isDirty());
		$this->assertEquals(
			array($dirty[0], $dirty[1]),
			$this->state->getDirtyMembers()
		);

		$this->assertFalse($this->state->isDirtyMember($dirty[2]));
		$this->assertSame($this->state, $this->state->markDirty($dirty[2]));
		$this->assertTrue($this->state->isDirtyMember($dirty[2]));

		/* 
		 * the state will only turn dirty when there are dirty members listed 
		 */
		$this->assertTrue($this->state->isDirty());
		$this->assertEquals(
			array($dirty[0], $dirty[1], $dirty[2]),
			$this->state->getDirtyMembers()
		);
	}

	/**
	 * When used with no parameters markCleans resets the state to marshaled
	 * and removes any dirty members
	 *
	 * @return null
	 */
	public function testMarkCleanDefaultNoParams()
	{
		$this->state->markDirty('member_1');
		$this->assertTrue($this->state->isDirty());
		$this->assertEquals(
			array('member_1'), 
			$this->state->getDirtyMembers()
		);

		$this->assertSame($this->state, $this->state->markClean());
		$this->assertTrue($this->state->isMarshal());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isNew());
		$this->assertFalse($this->state->isDelete());

		/* prove dirty members have been removed */
		$this->assertEquals(array(), $this->state->getDirtyMembers());

		$this->state->setState('delete');
		$this->assertTrue($this->state->isDelete());

		$this->assertSame($this->state, $this->state->markClean());
		$this->assertTrue($this->state->isMarshal());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isNew());
		$this->assertFalse($this->state->isDelete());

	
		$this->state->setState('new');
		$this->assertTrue($this->state->isNew());

		$this->assertSame($this->state, $this->state->markClean());
		$this->assertTrue($this->state->isMarshal());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isNew());
		$this->assertFalse($this->state->isDelete());

		$this->state->setState('marshal');
		$this->assertTrue($this->state->isMarshal());

		$this->assertSame($this->state, $this->state->markClean());
		$this->assertTrue($this->state->isMarshal());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isNew());
		$this->assertFalse($this->state->isDelete());
	}

	/**
	 * MarkClean can also be used to remove a flagged dirty member from the 
	 * list by giving it the members name. This method always returns a fluen
	 * interface.
	 *
	 * @return null
	 */
	public function testMarkCleanDirtyMember()
	{
		$dirty = array('member_1', 'member_2', 'member_3');
		$this->state->markDirty($dirty[0]);
		$this->state->markDirty($dirty[1]);
		$this->state->markDirty($dirty[2]);

		$this->assertTrue($this->state->isDirty());
		$this->assertTrue($this->state->isDirtyMember($dirty[0]));
		$this->assertTrue($this->state->isDirtyMember($dirty[1]));
		$this->assertTrue($this->state->isDirtyMember($dirty[2]));

		$this->assertSame($this->state, $this->state->markClean($dirty[0]));
		$this->assertTrue($this->state->isDirty());
		$this->assertFalse($this->state->isDirtyMember($dirty[0]));
		$this->assertTrue($this->state->isDirtyMember($dirty[1]));
		$this->assertTrue($this->state->isDirtyMember($dirty[2]));

		$this->assertSame($this->state, $this->state->markClean($dirty[1]));
		$this->assertTrue($this->state->isDirty());
		$this->assertFalse($this->state->isDirtyMember($dirty[0]));
		$this->assertFalse($this->state->isDirtyMember($dirty[1]));
		$this->assertTrue($this->state->isDirtyMember($dirty[2]));

		$this->assertSame($this->state, $this->state->markClean($dirty[2]));
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isDirtyMember($dirty[0]));
		$this->assertFalse($this->state->isDirtyMember($dirty[1]));
		$this->assertFalse($this->state->isDirtyMember($dirty[2]));

		/* when removing the last dirty item we enter a weird state
		 * where the state is dirty but isDirty is false because we made 
		 * changes but cleared them out
		 */
		$this->assertEquals('dirty', $this->state->getState());
	}

	/**
	 * @return	null
	 */
	public function testMarkNew()
	{
		$this->state->markDirty('member_1');
		$this->assertTrue($this->state->isDirty());

		$this->assertSame($this->state, $this->state->markNew());
		$this->assertEquals('new', $this->state->getState());
		$this->assertTrue($this->state->isNew());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isDelete());
		$this->assertFalse($this->state->isMarshal());

		$this->state->setState('marshal');
		$this->assertTrue($this->state->isMarshal());

		$this->assertSame($this->state, $this->state->markNew());
		$this->assertEquals('new', $this->state->getState());
		$this->assertTrue($this->state->isNew());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isDelete());
		$this->assertFalse($this->state->isMarshal());

		$this->state->setState('delete');
		$this->assertTrue($this->state->isDelete());

		$this->assertSame($this->state, $this->state->markNew());
		$this->assertEquals('new', $this->state->getState());
		$this->assertTrue($this->state->isNew());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isDelete());
		$this->assertFalse($this->state->isMarshal());
	}

	/**
	 * @return	null
	 */
	public function testMarkDelete()
	{
		$this->state->markDirty('member_1');
		$this->assertTrue($this->state->isDirty());

		$this->assertSame($this->state, $this->state->markDelete());
		$this->assertEquals('delete', $this->state->getState());
		$this->assertTrue($this->state->isDelete());
		$this->assertFalse($this->state->isNew());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isMarshal());

		$this->state->setState('marshal');
		$this->assertTrue($this->state->isMarshal());

		$this->assertSame($this->state, $this->state->markDelete());
		$this->assertEquals('delete', $this->state->getState());
		$this->assertTrue($this->state->isDelete());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isNew());
		$this->assertFalse($this->state->isMarshal());

		$this->state->setState('new');
		$this->assertTrue($this->state->isNew());

		$this->assertSame($this->state, $this->state->markDelete());
		$this->assertEquals('delete', $this->state->getState());
		$this->assertTrue($this->state->isDelete());
		$this->assertFalse($this->state->isNew());
		$this->assertFalse($this->state->isDirty());
		$this->assertFalse($this->state->isMarshal());
	}
}
