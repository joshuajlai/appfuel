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
namespace TestFuel\Unit\View\Compositor;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Compositor\TextCompositor;

/**
 * The text formmater converts and array of key=>value pairs into a string
 */
class TextCompositorTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var TextCompositor
	 */
	protected $compositor = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->compositor = new TextCompositor();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->compositor = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Compositor\ViewCompositorInterface',
			$this->compositor
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorDefaults()
	{
		$this->assertEquals(' ', $this->compositor->getKeyDelimiter());
		$this->assertEquals(' ', $this->compositor->getItemDelimiter());
		$this->assertEquals('assoc', $this->compositor->getArrayStrategy());

		$this->assertTrue($this->compositor->isFormatArrayAssoc());
		$this->assertFalse($this->compositor->isFormatArrayKeys());
		$this->assertFalse($this->compositor->isFormatArrayValues());
	
	}

	/**
	 * @return	null
	 */
	public function testArrayStrategyConstructor()
	{
		$compositor = new TextCompositor(null, null, 'assoc');
		$this->assertEquals('assoc', $compositor->getArrayStrategy());
		$this->assertTrue($compositor->isFormatArrayAssoc());
		$this->assertFalse($compositor->isFormatArrayKeys());
		$this->assertFalse($compositor->isFormatArrayValues());
	
		$compositor = new TextCompositor(null, null, 'keys');
		$this->assertEquals('keys', $compositor->getArrayStrategy());
		$this->assertFalse($compositor->isFormatArrayAssoc());
		$this->assertTrue($compositor->isFormatArrayKeys());
		$this->assertFalse($compositor->isFormatArrayValues());
			
		$compositor = new TextCompositor(null, null, 'values');
		$this->assertEquals('values', $compositor->getArrayStrategy());
		$this->assertFalse($compositor->isFormatArrayAssoc());
		$this->assertFalse($compositor->isFormatArrayKeys());
		$this->assertTrue($compositor->isFormatArrayValues());
		
		$compositor = new TextCompositor(null, null, null);
		$this->assertEquals('assoc', $compositor->getArrayStrategy());
		$this->assertTrue($compositor->isFormatArrayAssoc());
		$this->assertFalse($compositor->isFormatArrayKeys());
		$this->assertFalse($compositor->isFormatArrayValues());

		
		$this->assertEquals('assoc', $this->compositor->getArrayStrategy());
		$this->assertTrue($this->compositor->isFormatArrayAssoc());
		$this->assertFalse($this->compositor->isFormatArrayKeys());
		$this->assertFalse($this->compositor->isFormatArrayValues());	
	}

	/**
	 * @return	null
	 */	
	public function testArrayStrategy()
	{
		$this->assertEquals('assoc', $this->compositor->getArrayStrategy());
		$this->assertTrue($this->compositor->isFormatArrayAssoc());
		
		$this->assertSame(
			$this->compositor, 
			$this->compositor->setFormatArrayKeys(),
			'uses fluent interface'
		);
		$this->assertEquals('keys', $this->compositor->getArrayStrategy());
		$this->assertTrue($this->compositor->isFormatArrayKeys());
		$this->assertFalse($this->compositor->isFormatArrayValues());
		$this->assertFalse($this->compositor->isFormatArrayAssoc());
	
		$this->assertSame(
			$this->compositor, 
			$this->compositor->setFormatArrayValues(),
			'uses fluent interface'
		);
		$this->assertEquals('values', $this->compositor->getArrayStrategy());
		$this->assertFalse($this->compositor->isFormatArrayKeys());
		$this->assertTrue($this->compositor->isFormatArrayValues());
		$this->assertFalse($this->compositor->isFormatArrayAssoc());
			
		$this->assertSame(
			$this->compositor, 
			$this->compositor->setFormatArrayAssoc(),
			'uses fluent interface'
		);
		$this->assertEquals('assoc', $this->compositor->getArrayStrategy());
		$this->assertFalse($this->compositor->isFormatArrayKeys());
		$this->assertFalse($this->compositor->isFormatArrayValues());
		$this->assertTrue($this->compositor->isFormatArrayAssoc());
		
	}

	/**
	 * Test setting the delimiter for key value pairs
	 *
	 * @depends	testConstructorDefaults
	 * @return	null
	 */
	public function testSettingKeyDelimiter()
	{
		$compositor = new TextCompositor(',');
		$this->assertEquals(',', $compositor->getKeyDelimiter());
		
		$compositor = new TextCompositor('#');
		$this->assertEquals('#',  $compositor->getKeyDelimiter());

		/* should be the default */	
		$compositor = new TextCompositor(null);
		$this->assertEquals(' ',  $compositor->getKeyDelimiter());
	}
	
	/**
	 * Test setting the delimiter for each array item
	 *
	 * @depends	testSettingKeyDelimiter
	 * @return	null
	 */
	public function testSettingItemDelimiter()
	{
		$compositor = new TextCompositor(null, ',');
		$this->assertEquals(',', $compositor->getItemDelimiter());
		
		$compositor = new TextCompositor(null, '#');
		$this->assertEquals('#',  $compositor->getItemDelimiter());

		/* should be the default */	
		$compositor = new TextCompositor(null, null);
		$this->assertEquals(' ',  $compositor->getItemDelimiter());
	}

	/**
	 * Test setting both key and item delimiters
	 *
	 * @depends	testSettingItemDelimiter
	 * @return	null
	 */
	public function testSettingKeyItemDelimiter()
	{
		$compositor = new TextCompositor(',', ',');
		$this->assertEquals(',', $compositor->getItemDelimiter());
		$this->assertEquals(',', $compositor->getKeyDelimiter());
		
		$compositor = new TextCompositor('?', '?');
		$this->assertEquals('?',  $compositor->getItemDelimiter());
		$this->assertEquals('?',  $compositor->getKeyDelimiter());
		
		$compositor = new TextCompositor(null, null);
		$this->assertEquals(' ',  $compositor->getItemDelimiter());
		$this->assertEquals(' ',  $compositor->getKeyDelimiter());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testFormatEmptyArray()
	{
		$data = array();
		$this->assertEquals('', $this->compositor->compose($data));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testFormatAssociativeFormatAssoc()
	{
		$data = array(
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3'
		);

		$expected = 'key1 value1 key2 value2 key3 value3';
		$this->assertEquals($expected, $this->compositor->compose($data));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testFormatAssociativeFormatKeys()
	{
		$data = array(
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3'
		);
		
		$this->compositor->setFormatArrayKeys();

		$expected = 'key1 key2 key3';
		$this->assertEquals($expected, $this->compositor->compose($data));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testFormatAssociativeFormatValues()
	{
		$data = array(
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3'
		);
		$this->compositor->setFormatArrayValues();

		$expected = 'value1 value2 value3';
		$this->assertEquals($expected, $this->compositor->compose($data));
	}

	/**
	 * The default behavior is to treat arrays as key value pairs therefore
	 * the useArrayKeys is enabled by default which is why our results in
	 * this test looks odd. The formatter is giving index delimiter value
	 * 
	 * @return null
	 */
	public function testFormatArrayNotAssociative()
	{
		$data = array('values' => array(1,2,3,4));
		$expected = 'values 0 1 1 2 2 3 3 4';
		$this->assertEquals($expected, $this->compositor->compose($data));
	}

	/**
	 * @return	null
	 */
	public function testFormatArrayNotAssociativeFormatArrayValues()
	{
		$data = array('values' =>array(2,3,4));
		$this->compositor->setFormatArrayValues();
		
		$expected = '2 3 4';
		$this->assertEquals($expected, $this->compositor->compose($data));
	}

	/**
	 * @return	null
	 */
	public function testFormatArrayNotAssociativeFormatArrayKeys()
	{
		$data = array('key' => 1, 'my-key'=>2,'your-key' =>4);
		$this->compositor->setFormatArrayKeys();
		
		$expected = 'key my-key your-key';
		$this->assertEquals($expected, $this->compositor->compose($data));
	}

	/**
	 * Use a custom key and item delimiter
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAssociativeDataCustomFormatAssoc()
	{
		$compositor = new TextCompositor(':', ';');
		$data = array(
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3'
		);

		$expected = 'key1:value1;key2:value2;key3:value3';
		$this->assertEquals($expected, $compositor->compose($data));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAssociativeDataCustomFormatValues()
	{
		$compositor = new TextCompositor(':', ';', 'values');
		$data = array(
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3'
		);
	
		$expected = 'value1;value2;value3';
		$this->assertEquals($expected, $compositor->compose($data));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAssociativeDataCustomFormatKeys()
	{
		$compositor = new TextCompositor(':', ';', 'keys');
		$data = array(
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3'
		);
	
		$expected = 'key1:key2:key3';
		$this->assertEquals($expected, $compositor->compose($data));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterKeyDelimiterInt_Failure()
	{
		$compositor = new TextCompositor(1234);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterKeyDelimiterArray_Failure()
	{
		$compositor = new TextCompositor(array(1,2,3));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterKeyDelimiterObject_Failure()
	{
		$compositor = new TextCompositor(new StdClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterItemDelimiterInt_Failure()
	{
		$compositor = new TextCompositor(':', 1234);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterItemDelimiterArray_Failure()
	{
		$compositor = new TextCompositor(':', array(1,2,3));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterItemDelimiterObject_Failure()
	{
		$compositor = new TextCompositor(':', new StdClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadArrayStrategyInt_Failure()
	{
		$compositor = new TextCompositor(':', ':', 1234);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadArrayStrategyArray_Failure()
	{
		$compositor = new TextCompositor(':', array(1,2,3));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadArrayStrategyObject_Failure()
	{
		$compositor = new TextCompositor(':', new StdClass());
	}
}
