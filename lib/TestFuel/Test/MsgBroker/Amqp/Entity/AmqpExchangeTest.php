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
namespace TestFuel\Test\MsgBroker\Amqp\Entity;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\MsgBroker\Amqp\Entity\AmqpExchange;

/**
 * The exchange models the amqp exchange entity and does not perform any
 * operations. It is an immutable value object that can not be modified once
 * instantiated.
 */
class AmqpExchangeTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Exchange
	 */
	protected $exchange = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->exchange = new AmqpExchange();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->exchange = null;
	}

    /**
     * @return null
     */
    public function testInterface()
    {
        $this->assertInstanceOf(
            'Appfuel\Framework\MsgBroker\Amqp\AmqpExchangeInterface',
            $this->exchange
        );
    }

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultValues()
	{
		$this->assertEquals('', $this->exchange->getExchangeName());
		$this->assertEquals('direct', $this->exchange->getType());
		$this->assertFalse($this->exchange->isPassive());
		$this->assertFalse($this->exchange->isDurable());
		$this->assertTrue($this->exchange->isAutoDelete());
		$this->assertFalse($this->exchange->isInternal());
		$this->assertFalse($this->exchange->isNoWait());
		$this->assertNull($this->exchange->getArguments());
		$this->assertNull($this->exchange->getTicket());
	}

	/**
	 * @return	array
	 */
	public function provideValidNames()
	{
		return	array(
			array('my-exchange', 'my-exchange'),
			array('', ''),
			array(null, '')
		);
	}

	/**
	 * @dataProvider	provideValidNames
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_ExchangeName($input, $expected)
	{
		$exchange = new AmqpExchange($input);
		$this->assertEquals($expected, $exchange->getExchangeName());
	}

	/**
	 * @return	array
	 */
	public function provideInvalidNames()
	{
		return	array(
			array(array(1,2,3)),
			array(array()),
			array(new StdClass()),
			array(123454),
			array(1.2345)
		);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInValidNames
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_ExchangeNameFailures($input)
	{
		$exchange = new AmqpExchange($input);
	}



	/**
	 * @return	array
	 */
	public function provideValidTypeValues()
	{
		return array(
			array('direct', 'direct'),
			array('DIRECT', 'direct'),
			array('Direct',	'direct'),
			array('topic',	'topic'),
			array('TOPIC',	'topic'),
			array('Topic',	'topic'),
			array('fanout',	'fanout'),
			array('FANOUT',	'fanout'),
			array('FanOut',	'fanout'),
			array('header',	'header'),
			array('HEADER',	'header'),
			array('HeADer',	'header'),
			array(null,		'direct')
		);
	}

	/**
	 * @dataProvider	provideValidTypeValues
	 * @depends			testInterface
	 * @return			null
	 */
	public function testConstructor_Type($input, $expected)
	{
		$exchange = new AmqpExchange(null, $input);
		$this->assertEquals($expected, $exchange->getType());
	}

	/**
	 * @return	array	
	 */
	public function provideInvalidTypeValues()
	{
		return array(
			array('no-in-set'),
			array(' '),
			array(''),
			array(array()),
			array(array(1,2,3)),
			array(1232),
			array(1.23),
			array(new StdClass())
		);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInValidTypeValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_FailedTypeValues($input)
	{
		$exchange = new AmqpExchange(null, $input);
	}

	/**
	 * @return	array	
	 */
	public function provideBoolValues()
	{
		return array(
			array(true),
			array(false),
		);
	}


	/**
	 * @return	array	
	 */
	public function provideInvalidBoolValues()
	{
		return array(
			array('string-is-not-bool'),
			array('true'),
			array('false'),
			array(''),
			array(array()),
			array(array(1,2,3)),
			array(1),
			array(0),
			array(new StdClass())
		);
	}


	/**
	 * @dataProvider		provideBoolValues
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_Passive($input)
	{
		$exchange = new AmqpExchange(null, null, $input);
		$this->assertEquals($input, $exchange->isPassive());
		
		$exchange = new AmqpExchange(null, null, null);
		$this->assertFalse($exchange->isPassive());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidBoolValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_PassiveFailures($input)
	{
		$exchange = new AmqpExchange(null, null, $input);
	}

	/**
	 * @dataProvider		provideBoolValues
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_Durable($input)
	{
		$exchange = new AmqpExchange(null, null, null, $input);
		$this->assertEquals($input, $exchange->isDurable());
		
		$exchange = new AmqpExchange(null, null, null, null);
		$this->assertFalse($exchange->isDurable());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidBoolValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_DurableFailures($input)
	{
		$exchange = new AmqpExchange(null, null, null, $input);
	}

	/**
	 * @dataProvider		provideBoolValues
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_AutoDelete($input)
	{
		$exchange = new AmqpExchange(null, null, null, null, $input);
		$this->assertEquals($input, $exchange->isAutoDelete());
		
		$exchange = new AmqpExchange(null, null, null, null, null);
		$this->assertTrue($exchange->isAutoDelete());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidBoolValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_AutoDeleteFailures($input)
	{
		$exchange = new AmqpExchange(null, null, null, null, $input);
	}

	/**
	 * @dataProvider		provideBoolValues
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_Internal($input)
	{
		$exchange = new AmqpExchange(null, null, null, null, null, $input);
		$this->assertEquals($input, $exchange->isInternal());
		
		$exchange = new AmqpExchange(null, null, null, null, null, null);
		$this->assertFalse($exchange->isInternal());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidBoolValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_InternalFailures($input)
	{
		$exchange = new AmqpExchange(null, null, null, null, null, $input);
	}

	/**
	 * @dataProvider		provideBoolValues
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_NoWait($in)
	{
		$exchange = new AmqpExchange(null, null, null, null, null, null, $in);
		$this->assertEquals($in, $exchange->isNoWait());
		
		$exchange = new AmqpExchange(null, null, null, null, null, null, null);
		$this->assertFalse($exchange->isNoWait());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidBoolValues
	 * @depends				testInterface
	 * @return				null
	 */
	public function testConstructor_NoWaitFailures($input)
	{
		$ex = new AmqpExchange(null, null, null, null, null, null, $input);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor_Args()
	{
		$args = array('a', 'b', 'c');
		$ex = new AmqpExchange(null,null,null,null,null,null,null, $args);
		$this->assertEquals($args, $ex->getArguments()); 

		/* array kind does not matter */
		$args = array('a'=> 'x', 'b'=>'y', 'c'=>'ww');
		$ex = new AmqpExchange(null,null,null,null,null,null,null, $args);
		$this->assertEquals($args, $ex->getArguments()); 

		/* empty arrays are not excepted */
		$args = array();
		$ex = new AmqpExchange(null,null,null,null,null,null,null, $args);
		$this->assertNull($ex->getArguments()); 

		$ex = new AmqpExchange(null,null,null,null,null,null,null,null);
		$this->assertNull($ex->getArguments()); 

		/* anything not an array is ignored */
		$args = 12345;
		$ex = new AmqpExchange(null,null,null,null,null,null,null, $args);
		$this->assertNull($ex->getArguments()); 

		$args = 'i am a string';
		$ex = new AmqpExchange(null,null,null,null,null,null,null, $args);
		$this->assertNull($ex->getArguments()); 

		$args = new StdClass();
		$ex = new AmqpExchange(null,null,null,null,null,null,null, $args);
		$this->assertNull($ex->getArguments()); 	
	}

	/**
	 * @return	null
	 */
	public function testConstructor_Ticket()
	{
		$tic = 1234;
		$ex = new AmqpExchange(null,null,null,null,null,null,null,null,$tic);
		$this->assertEquals($tic, $ex->getTicket()); 

		$tic = 0;
		$ex = new AmqpExchange(null,null,null,null,null,null,null,null,$tic);
		$this->assertEquals($tic, $ex->getTicket()); 

		$tic = -12345;
		$ex = new AmqpExchange(null,null,null,null,null,null,null,null,$tic);
		$this->assertEquals($tic, $ex->getTicket()); 

		$tic = 'i am a string';
		$ex = new AmqpExchange(null,null,null,null,null,null,null,null,$tic);
		$this->assertNull($ex->getTicket()); 

		$tic = 1.23;
		$ex = new AmqpExchange(null,null,null,null,null,null,null,null,$tic);
		$this->assertNull($ex->getTicket()); 

		$tic = array(1,2,3);
		$ex = new AmqpExchange(null,null,null,null,null,null,null,null,$tic);
		$this->assertNull($ex->getTicket()); 

		$tic = new StdClass();
		$ex = new AmqpExchange(null,null,null,null,null,null,null,null,$tic);
		$this->assertNull($ex->getTicket()); 





	}
}
