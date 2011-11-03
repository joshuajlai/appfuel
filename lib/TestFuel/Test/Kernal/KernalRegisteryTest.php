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
namespace TestFuel\Test\Kernal;

use StdClass,
	Appfuel\Kernal\KernalRegistry,
	TestFuel\TestCase\BaseTestCase;

/**
 * The kernal registry is used to parameters used to startup the framework
 * and also maintain domain map of domain-key to domain class, so developers
 * can refer to this key when referring to domains
 */
class KernalRegistryTest extends BaseTestCase
{
	/**
	 * Back up the data in the registry
	 * @var string
	 */
	protected $backupData = null;

	/**
	 * Backup the registry data then initialize it with an empty bag
	 * @return null
	 */
	public function setUp()
	{
		$this->backUpParams  = KernalRegistry::getParams();
		$this->backUpDomains = KernalRegistry::getDomainMap();
		KernalRegistry::clear();
	}

	/**
	 * Restore the original registry data
	 * @return null
	 */
	public function tearDown()
	{
		KernalRegistry::setParams($this->backUpParams);
		KernalRegistry::setDomainMap($this->backUpDomains);
	}

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
		$this->assertEquals(array(), KernalRegistry::getParams());
		$this->assertFalse(KernalRegistry::isParam($key));
		$this->assertNull(KernalRegistry::addParam($key, $value));
		$this->assertTrue(KernalRegistry::isParam($key));
		$this->assertEquals($value, KernalRegistry::getParam($key));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideBadKey
	 * @return	null
	 */
	public function testAddBadKey($key)
	{
		$this->assertNull(KernalRegistry::addParam($key, 'sometext'));
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
		$this->assertNull(KernalRegistry::setParams($list));
		$this->assertEquals($list, KernalRegistry::getParams());

		/* values get overwritten */
		$new = $list;
		$new['param-a'] = 'my-new-value';
		$this->assertNull(KernalRegistry::setParams($new));
		$this->assertEquals($new, KernalRegistry::getParams());

		$this->assertNull(KernalRegistry::clearParams());
		$this->assertEquals(array(), KernalRegistry::getParams());

		/* empty list when already empty*/
		$this->assertEquals(array(), KernalRegistry::getParams());
		$this->assertNull(KernalRegistry::setParams(array()));
		$this->assertEquals(array(), KernalRegistry::getParams());

		/* empty list when populated */
		KernalRegistry::setParams($list);
		KernalRegistry::setParams(array());
		$this->assertEquals($list, KernalRegistry::getParams());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetParamsNotAssociativeArray()
	{
		$list = array('value-1','value-2','value-3','value-4');
		KernalRegistry::setParams($list);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetParamsNotAssociativeMixedArray()
	{
		$list = array('value-1','value-2','value-3',1=>'value-4');
		KernalRegistry::setParams($list);
	}

    /**
     * @dataProvider    provideValidDomainKeyValues
     * @return  null
     */
    public function testAddDomainClass($key, $class)
    {
        $this->assertEquals(array(), KernalRegistry::getDomainMap());
        $this->assertFalse(KernalRegistry::isDomainClass($key));
        $this->assertNull(KernalRegistry::addDomainClass($key, $class));
        $this->assertTrue(KernalRegistry::isDomainClass($key));
        $this->assertEquals($class, KernalRegistry::getDomainClass($key));
    }

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideBadKey
	 * @return	null
	 */
	public function testAddBadDomainKey($key)
	{
		KernalRegistry::addDomainClass($key, 'MyDomain');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideBadKey
	 * @return	null
	 */
	public function testAddBadDomainClass($class)
	{
		KernalRegistry::addDomainClass('my-key', $class);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetDomainMapNotAssociativeArray()
	{
		$list = array('value-1','value-2','value-3','value-4');
		KernalRegistry::setDomainMap($list);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testSetDomainMapNotAssociativeMixedArray()
	{
		$list = array('value-1','value-2','value-3',1=>'value-4');
		KernalRegistry::setDomainMap($list);
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
		$this->assertNull(KernalRegistry::setDomainMap($list));
		$this->assertEquals($list, KernalRegistry::getDomainMap());

		/* values get overwritten */
		$new = $list;
		$new['key-a'] = 'NewDomain';
		$this->assertNull(KernalRegistry::setDomainMap($new));
		$this->assertEquals($new, KernalRegistry::getDomainMap());

		$this->assertNull(KernalRegistry::clearDomainMap());
		$this->assertEquals(array(), KernalRegistry::getDomainMap());

		/* empty list when already empty*/
		$this->assertEquals(array(), KernalRegistry::getDomainMap());
		$this->assertNull(KernalRegistry::setDomainMap(array()));
		$this->assertEquals(array(), KernalRegistry::getDomainMap());

		/* empty list when populated */
		KernalRegistry::setDomainMap($list);
		KernalRegistry::setDomainMap(array());
		$this->assertEquals($list, KernalRegistry::getDomainMap());
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
		KernalRegistry::setDomainMap($list);
		$list2 = array(
			'param-a' => 'value-1',
			'param-b' => 'value-2',
			'param-c' => 'value-3',
			'param-d' => 'value-4'
		);
		KernalRegistry::setParams($list2);

		$this->assertNull(KernalRegistry::clear());
		$this->assertEquals(array(), KernalRegistry::getParams());
		$this->assertEquals(array(), KernalRegistry::getDomainMap());
		
	}
}

