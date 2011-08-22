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
namespace Test\Appfuel\App\Action;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\App\Action\ActionBuilder,
	StdClass;

/**
 * The action builder encapsulates the logic needed to build a fully functional
 * action controller.
 */
class ActionBuilderTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var ActionBuilder
	 */
	protected $builder = null;

	/**
	 * @var Route
	 */
	protected $route = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->route   = $this->getMockRoute();
		$this->builder = new ActionBuilder($this->route);
	}

	/**
	 * Restore the super global data
	 * 
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->route);
		unset($this->builder);
	}

	/**
	 * @return null
	 */
	public function testContructorGetRoute()
	{
		$this->assertSame($this->route, $this->builder->getRoute());
	}

	/**
	 * @return null
	 */
	public function testGetSetIsError()
	{
		$this->assertFalse($this->builder->isError());
		$this->assertNull($this->builder->getError());

		$error = 'this is an error';
		$this->assertSame(
			$this->builder,
			$this->builder->setError($error),
			'must use a fluent interface'
		);

		$this->assertTrue($this->builder->isError());
		$this->assertEquals($error, $this->builder->getError());

		$this->assertSame(
			$this->builder,
			$this->builder->clearError(),
			'must use a fluent interface'
		);

		$this->assertFalse($this->builder->isError());
		$this->assertNull($this->builder->getError());
	}

	/**
	 * @return null
	 */
	public function testIsEnableDisableInputValidation()
	{
		$this->assertTrue($this->builder->isInputValidation());
		$this->assertSame(
			$this->builder,
			$this->builder->disableInputValidation(),
			'must use a fluent interface'
		);
		$this->assertFalse($this->builder->isInputValidation());

		$this->assertSame(
			$this->builder,
			$this->builder->enableInputValidation(),
			'must use a fluent interface'
		);
		$this->assertTrue($this->builder->isInputValidation());
	}

    /**
     * @return null
     */
    public function testIsGetClearRemoveValidResponseTypes()
    {
        $defaultTypes = array(
            'Html',
            'Json',
            'Cli',
            'Csv'
        );

        $this->assertEquals(
            $defaultTypes,
            $this->builder->getValidResponseTypes()
        );

        foreach ($defaultTypes as $type) {
            $this->assertTrue($this->builder->isValidResponseType($type));
        }

        $this->assertSame(
            $this->builder,
            $this->builder->clearValidResponseTypes(),
            'must use a fluent interface'
        );

        $this->assertEquals(
            array(),
            $this->builder->getValidResponseTypes(),
            'should now be empty because we cleared the types'
        );

    }

    /**
     * @return null
     */
    public function testSetValidResponseTypes()
    {
        $this->builder->clearValidResponseTypes();

        $types = array(
            'my-type',
            'your-type',
            'his-type',
            'her-type'
        );

        foreach ($types as $type) {
            $this->assertFalse($this->builder->isValidResponseType($type));
        }

        $this->assertSame(
            $this->builder,
            $this->builder->setValidResponseTypes($types),
            'must use fluent interface'
        );

        foreach ($types as $type) {
            $this->assertTrue($this->builder->isValidResponseType($type));
        }

        $this->assertEquals(
            $types,
            $this->builder->getValidResponseTypes(),
            'should now be the types we just set'
        );
    }

    /**
     * @return null
     */
    public function testAddRemoveValidReturnTypes()
    {
        $this->builder->clearValidResponseTypes();

        $type = 'my-type';
        $this->assertSame(
            $this->builder,
            $this->builder->addValidResponseType($type),
            'must use a fluent interface'
        );

        $expected = array($type);
        $this->assertEquals(
            $expected,
            $this->builder->getValidResponseTypes(),
            'should now be the one type added'
        );

        $this->assertTrue($this->builder->isValidResponseType($type));

        $type2 = 'my-second-type';
        $this->builder->addValidResponseType($type2);

        $expected = array($type, $type2);
        $this->assertEquals(
            $expected,
            $this->builder->getValidResponseTypes(),
            'should have both types that were added'
        );

        $this->assertTrue($this->builder->isValidResponseType($type));
        $this->assertTrue($this->builder->isValidResponseType($type2));

        $this->assertSame(
            $this->builder,
            $this->builder->removeValidResponseType($type),
            'must use a fluent interface'
        );
        $this->assertFalse($this->builder->isValidResponseType($type));

        $expected = array($type2);
        $this->assertEquals(
            $expected,
            $this->builder->getValidResponseTypes(),
            'should now only have the second type'
        );


        $this->builder->removeValidResponseType($type2);
        $this->assertFalse($this->builder->isValidResponseType($type2));

        $this->assertEquals(
            array(),
            $this->builder->getValidResponseTypes(),
            'should now be empty because we removed all types'
        );
    }

	/**
	 * @return null
	 */
	public function testCreateViewManager()
	{
		$this->assertInstanceOf(
			'Appfuel\App\View\ViewManager',
			$this->builder->createViewManager()
		);
	}

    /**
     * @expectedException   Appfuel\Framework\Exception
     */
    public function testAddValidResponseTypeBadTypeEmptyString()
    {
        $this->builder->addValidResponseType('');
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     */
    public function testAddValidResponseTypeBadTypeArray()
    {
        $this->builder->addValidResponseType(array());
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     */
    public function testAddValidResponseTypeBadTypeInt()
    {
        $this->builder->addValidResponseType(12345);
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     */
    public function testAddValidResponseTypeBadTypeObject()
    {
        $this->builder->addValidResponseType(new stdClass());
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     */
    public function testRemoveValidResponseTypeBadTypeEmptyString()
    {
        $this->builder->removeValidResponseType('');
    }


    /**
     * @expectedException   Appfuel\Framework\Exception
     */
    public function testRemoveValidResponseTypeBadTypeArray()
    {
        $this->builder->removeValidResponseType(array());
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     */
    public function testRemoveValidResponseTypeBadTypeInt()
    {
        $this->builder->removeValidResponseType(12345);
    }

    /**
     * @expectedException   Appfuel\Framework\Exception
     */
    public function testRemoveValidResponseTypeBadTypeObject()
    {
        $this->builder->removeValidResponseType(new stdClass());
    }

}

