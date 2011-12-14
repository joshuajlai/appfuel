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
namespace TestFuel\Unit\View\Formatter;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Formatter\TextFormatter;

/**
 * The text formmater converts and array of key=>value pairs into a string
 */
class TextFormatterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var TextFormatter
	 */
	protected $formatter = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->formatter = new TextFormatter();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->formatter = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Formatter\ViewFormatterInterface',
			$this->formatter
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorDefaults()
	{
		$this->assertEquals(' ', $this->formatter->getKeyDelimiter());
		$this->assertEquals(' ', $this->formatter->getItemDelimiter());
		$this->assertEquals('assoc', $this->formatter->getArrayStrategy());

		$this->assertTrue($this->formatter->isFormatArrayAssoc());
		$this->assertFalse($this->formatter->isFormatArrayKeys());
		$this->assertFalse($this->formatter->isFormatArrayValues());
	
	}

	/**
	 * @return	null
	 */
	public function testArrayStrategyConstructor()
	{
		$formatter = new TextFormatter(null, null, 'assoc');
		$this->assertEquals('assoc', $formatter->getArrayStrategy());
		$this->assertTrue($formatter->isFormatArrayAssoc());
		$this->assertFalse($formatter->isFormatArrayKeys());
		$this->assertFalse($formatter->isFormatArrayValues());
	
		$formatter = new TextFormatter(null, null, 'keys');
		$this->assertEquals('keys', $formatter->getArrayStrategy());
		$this->assertFalse($formatter->isFormatArrayAssoc());
		$this->assertTrue($formatter->isFormatArrayKeys());
		$this->assertFalse($formatter->isFormatArrayValues());
			
		$formatter = new TextFormatter(null, null, 'values');
		$this->assertEquals('values', $formatter->getArrayStrategy());
		$this->assertFalse($formatter->isFormatArrayAssoc());
		$this->assertFalse($formatter->isFormatArrayKeys());
		$this->assertTrue($formatter->isFormatArrayValues());
		
		$formatter = new TextFormatter(null, null, null);
		$this->assertEquals('assoc', $formatter->getArrayStrategy());
		$this->assertTrue($formatter->isFormatArrayAssoc());
		$this->assertFalse($formatter->isFormatArrayKeys());
		$this->assertFalse($formatter->isFormatArrayValues());

		
		$this->assertEquals('assoc', $this->formatter->getArrayStrategy());
		$this->assertTrue($this->formatter->isFormatArrayAssoc());
		$this->assertFalse($this->formatter->isFormatArrayKeys());
		$this->assertFalse($this->formatter->isFormatArrayValues());	
	}

	/**
	 * @return	null
	 */	
	public function testArrayStrategy()
	{
		$this->assertEquals('assoc', $this->formatter->getArrayStrategy());
		$this->assertTrue($this->formatter->isFormatArrayAssoc());
		
		$this->assertSame(
			$this->formatter, 
			$this->formatter->setFormatArrayKeys(),
			'uses fluent interface'
		);
		$this->assertEquals('keys', $this->formatter->getArrayStrategy());
		$this->assertTrue($this->formatter->isFormatArrayKeys());
		$this->assertFalse($this->formatter->isFormatArrayValues());
		$this->assertFalse($this->formatter->isFormatArrayAssoc());
	
		$this->assertSame(
			$this->formatter, 
			$this->formatter->setFormatArrayValues(),
			'uses fluent interface'
		);
		$this->assertEquals('values', $this->formatter->getArrayStrategy());
		$this->assertFalse($this->formatter->isFormatArrayKeys());
		$this->assertTrue($this->formatter->isFormatArrayValues());
		$this->assertFalse($this->formatter->isFormatArrayAssoc());
			
		$this->assertSame(
			$this->formatter, 
			$this->formatter->setFormatArrayAssoc(),
			'uses fluent interface'
		);
		$this->assertEquals('assoc', $this->formatter->getArrayStrategy());
		$this->assertFalse($this->formatter->isFormatArrayKeys());
		$this->assertFalse($this->formatter->isFormatArrayValues());
		$this->assertTrue($this->formatter->isFormatArrayAssoc());
		
	}

	/**
	 * Test setting the delimiter for key value pairs
	 *
	 * @depends	testConstructorDefaults
	 * @return	null
	 */
	public function testSettingKeyDelimiter()
	{
		$formatter = new TextFormatter(',');
		$this->assertEquals(',', $formatter->getKeyDelimiter());
		
		$formatter = new TextFormatter('#');
		$this->assertEquals('#',  $formatter->getKeyDelimiter());

		/* should be the default */	
		$formatter = new TextFormatter(null);
		$this->assertEquals(' ',  $formatter->getKeyDelimiter());
	}
	
	/**
	 * Test setting the delimiter for each array item
	 *
	 * @depends	testSettingKeyDelimiter
	 * @return	null
	 */
	public function testSettingItemDelimiter()
	{
		$formatter = new TextFormatter(null, ',');
		$this->assertEquals(',', $formatter->getItemDelimiter());
		
		$formatter = new TextFormatter(null, '#');
		$this->assertEquals('#',  $formatter->getItemDelimiter());

		/* should be the default */	
		$formatter = new TextFormatter(null, null);
		$this->assertEquals(' ',  $formatter->getItemDelimiter());
	}

	/**
	 * Test setting both key and item delimiters
	 *
	 * @depends	testSettingItemDelimiter
	 * @return	null
	 */
	public function testSettingKeyItemDelimiter()
	{
		$formatter = new TextFormatter(',', ',');
		$this->assertEquals(',', $formatter->getItemDelimiter());
		$this->assertEquals(',', $formatter->getKeyDelimiter());
		
		$formatter = new TextFormatter('?', '?');
		$this->assertEquals('?',  $formatter->getItemDelimiter());
		$this->assertEquals('?',  $formatter->getKeyDelimiter());
		
		$formatter = new TextFormatter(null, null);
		$this->assertEquals(' ',  $formatter->getItemDelimiter());
		$this->assertEquals(' ',  $formatter->getKeyDelimiter());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testFormatEmptyArray()
	{
		$data = array();
		$this->assertEquals('', $this->formatter->format($data));
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
		$this->assertEquals($expected, $this->formatter->format($data));
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
		
		$this->formatter->setFormatArrayKeys();

		$expected = 'key1 key2 key3';
		$this->assertEquals($expected, $this->formatter->format($data));
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
		$this->formatter->setFormatArrayValues();

		$expected = 'value1 value2 value3';
		$this->assertEquals($expected, $this->formatter->format($data));
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
		$this->assertEquals($expected, $this->formatter->format($data));
	}

	/**
	 * @return	null
	 */
	public function testFormatArrayNotAssociativeFormatArrayValues()
	{
		$data = array('values' =>array(2,3,4));
		$this->formatter->setFormatArrayValues();
		
		$expected = '2 3 4';
		$this->assertEquals($expected, $this->formatter->format($data));
	}

	/**
	 * @return	null
	 */
	public function testFormatArrayNotAssociativeFormatArrayKeys()
	{
		$data = array('key' => 1, 'my-key'=>2,'your-key' =>4);
		$this->formatter->setFormatArrayKeys();
		
		$expected = 'key my-key your-key';
		$this->assertEquals($expected, $this->formatter->format($data));
	}

	/**
	 * Use a custom key and item delimiter
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAssociativeDataCustomFormatAssoc()
	{
		$formatter = new TextFormatter(':', ';');
		$data = array(
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3'
		);

		$expected = 'key1:value1;key2:value2;key3:value3';
		$this->assertEquals($expected, $formatter->format($data));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAssociativeDataCustomFormatValues()
	{
		$formatter = new TextFormatter(':', ';', 'values');
		$data = array(
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3'
		);
	
		$expected = 'value1;value2;value3';
		$this->assertEquals($expected, $formatter->format($data));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAssociativeDataCustomFormatKeys()
	{
		$formatter = new TextFormatter(':', ';', 'keys');
		$data = array(
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3'
		);
	
		$expected = 'key1:key2:key3';
		$this->assertEquals($expected, $formatter->format($data));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterKeyDelimiterInt_Failure()
	{
		$formatter = new TextFormatter(1234);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterKeyDelimiterArray_Failure()
	{
		$formatter = new TextFormatter(array(1,2,3));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterKeyDelimiterObject_Failure()
	{
		$formatter = new TextFormatter(new StdClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterItemDelimiterInt_Failure()
	{
		$formatter = new TextFormatter(':', 1234);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterItemDelimiterArray_Failure()
	{
		$formatter = new TextFormatter(':', array(1,2,3));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadDelimiterItemDelimiterObject_Failure()
	{
		$formatter = new TextFormatter(':', new StdClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadArrayStrategyInt_Failure()
	{
		$formatter = new TextFormatter(':', ':', 1234);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadArrayStrategyArray_Failure()
	{
		$formatter = new TextFormatter(':', array(1,2,3));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testBadArrayStrategyObject_Failure()
	{
		$formatter = new TextFormatter(':', new StdClass());
	}
}
