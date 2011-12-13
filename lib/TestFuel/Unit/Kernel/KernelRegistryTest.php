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
namespace TestFuel\Unit\Kernel;

use StdClass,
	Appfuel\Kernel\KernelRegistry,
	TestFuel\TestCase\BaseTestCase;

/**
 * The kernal registry is used to parameters used to startup the framework
 * and also maintain domain map of domain-key to domain class, so developers
 * can refer to this key when referring to domains
 */
class KernelRegistryTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideValidParams()
	{
		return	array(
			array('key',	'str-value'),
			array('key-a',	12345),
			array('key-b',	1.234),
			array('key-c',	array()),
			array('key-d',	array(1,2,3)),
			array('key-e',	array('a'=>'b', 'c' => array(1,2,3))),
			array('key-f',	new StdClass()),
		);
	}

	/**
	 * @return	array
	 */
	public function provideBadKey()
	{
		return array(
			array(0),
			array(1),
			array(100),
			array(-1),
			array(-100),
			array(1.2),
			array(''),
			array(' '),
			array("\t"),
			array("\n"),
			array(" \t"),
			array(" \n"),
			array(" \t\n"),
			array(array()),
			array(array(1,2,3)),
			array(new StdClass())
		);	
	}

	/**
	 * @return	array
	 */
	public function provideValidDomainKeyValues()
	{
		return	array(
			array('key',	'MyClass'),
			array('key-a',	'My\Class'),
			array('key-b',	'My\Other\Class'),
			array('key-c',	'My\Other\Class\Name'),
		);
	}

	/**
	 * @dataProvider	provideValidParams
	 * @return	null
	 */
	public function testAddGetIsParam($key, $value)
	{
		$this->assertEquals(array(), KernelRegistry::getParams());
		$this->assertFalse(KernelRegistry::isParam($key));
		$this->assertNull(KernelRegistry::addParam($key, $value));
		$this->assertTrue(KernelRegistry::isParam($key));
		$this->assertEquals($value, KernelRegistry::getParam($key));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideBadKey
	 * @return	null
	 */
	public function testAddBadKey($key)
	{
		$this->assertNull(KernelRegistry::addParam($key, 'sometext'));
	}

	/**
	 * @return	null
	 */
	public function testSetClearParams()
	{
		$list = array(
			'param-a' => 'value-1',
			'param-b' => 'value-2',
			'param-c' => 'value-3',
			'param-d' => 'value-4'
		);
		$this->assertNull(KernelRegistry::setParams($list));
		$this->assertEquals($list, KernelRegistry::getParams());

		/* values get overwritten */
		$new = $list;
		$new['param-a'] = 'my-new-value';
		$this->assertNull(KernelRegistry::setParams($new));
		$this->assertEquals($new, KernelRegistry::getParams());

		$this->assertNull(KernelRegistry::clearParams());
		$this->assertEquals(array(), KernelRegistry::getParams());

		/* empty list when already empty*/
		$this->assertEquals(array(), KernelRegistry::getParams());
		$this->assertNull(KernelRegistry::setParams(array()));
		$this->assertEquals(array(), KernelRegistry::getParams());

		/* empty list when populated */
		KernelRegistry::setParams($list);
		KernelRegistry::setParams(array());
		$this->assertEquals($list, KernelRegistry::getParams());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testSetParamsNotAssociativeArray()
	{
		$list = array('value-1','value-2','value-3','value-4');
		KernelRegistry::setParams($list);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testSetParamsNotAssociativeMixedArray()
	{
		$list = array('value-1','value-2','value-3',1=>'value-4');
		KernelRegistry::setParams($list);
	}

    /**
     * @dataProvider    provideValidDomainKeyValues
     * @return  null
     */
    public function testAddDomainClass($key, $class)
    {
        $this->assertEquals(array(), KernelRegistry::getDomainMap());
        $this->assertFalse(KernelRegistry::isDomainClass($key));
        $this->assertNull(KernelRegistry::addDomainClass($key, $class));
        $this->assertTrue(KernelRegistry::isDomainClass($key));
        $this->assertEquals($class, KernelRegistry::getDomainClass($key));
    }

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideBadKey
	 * @return	null
	 */
	public function testAddBadDomainKey($key)
	{
		KernelRegistry::addDomainClass($key, 'MyDomain');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideBadKey
	 * @return	null
	 */
	public function testAddBadDomainClass($class)
	{
		KernelRegistry::addDomainClass('my-key', $class);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testSetDomainMapNotAssociativeArray()
	{
		$list = array('value-1','value-2','value-3','value-4');
		KernelRegistry::setDomainMap($list);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testSetDomainMapNotAssociativeMixedArray()
	{
		$list = array('value-1','value-2','value-3',1=>'value-4');
		KernelRegistry::setDomainMap($list);
	}

	/**
	 * @return	null
	 */
	public function testSetClearDomainMap()
	{
		$list = array(
			'key-a' => 'MyClass',
			'key-b' => 'My\Class',
			'key-c' => 'My\Class\Name',
			'key-d' => 'My\Class\Domain\Class'
		);
		$this->assertNull(KernelRegistry::setDomainMap($list));
		$this->assertEquals($list, KernelRegistry::getDomainMap());

		/* values get overwritten */
		$new = $list;
		$new['key-a'] = 'NewDomain';
		$this->assertNull(KernelRegistry::setDomainMap($new));
		$this->assertEquals($new, KernelRegistry::getDomainMap());

		$this->assertNull(KernelRegistry::clearDomainMap());
		$this->assertEquals(array(), KernelRegistry::getDomainMap());

		/* empty list when already empty*/
		$this->assertEquals(array(), KernelRegistry::getDomainMap());
		$this->assertNull(KernelRegistry::setDomainMap(array()));
		$this->assertEquals(array(), KernelRegistry::getDomainMap());

		/* empty list when populated */
		KernelRegistry::setDomainMap($list);
		KernelRegistry::setDomainMap(array());
		$this->assertEquals($list, KernelRegistry::getDomainMap());
	}

	/**
	 * @return	null
	 */
	public function testClear()
	{
		$list = array(
			'key-a' => 'MyClass',
			'key-b' => 'My\Class',
			'key-c' => 'My\Class\Name',
			'key-d' => 'My\Class\Domain\Class'
		);
		KernelRegistry::setDomainMap($list);

		$list2 = array(
			'param-a' => 'value-1',
			'param-b' => 'value-2',
			'param-c' => 'value-3',
			'param-d' => 'value-4'
		);
		KernelRegistry::setParams($list2);

		$list3 = array(
			'MyClass',
			'YourClass',
			'My\Class',
			'Your\Class'
		);

		$this->assertNull(KernelRegistry::clear());
		$this->assertEquals(array(), KernelRegistry::getParams());
		$this->assertEquals(array(), KernelRegistry::getDomainMap());
	}
}

