<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Testfuel\Unit\Console;

use StdClass,
	Appfuel\Console\ArgSpec,
	Testfuel\TestCase\BaseTestCase;

class ArgSpecTest extends BaseTestCase
{
	/**
	 * Default parameter delimiters
	 * @var string
	 */
	protected $defaultDelims = array('', ' ', '=');

	/**
	 * @return	array
	 */
	public function provideInvalidShort()
	{
		return array(
			array('some long text'),
			array('aaa'),
			array('aa'),
			array(' '),
		);
	}

	/**
	 * @param	string	$name	name of the arg spec
	 * @param	string	$short	short option text
	 * @param	string	$long	long option text
	 * @return	string
	 */
	public function createDefaultError($name, $short, $long)
	{
		$text  = "cli arg specification failed for -($name): ";
		$text .= "short option: -($short) long option: -($long)";
		return $text;
	}

	/**
	 * @return	array
	 */
	public function getDefaultParamDelimiters()
	{
		return $this->defaultDelims;
	}

	/**
	 * @test
	 * @return	array
	 */
	public function onlyShortOpt()
	{
		$data = array(
			'name'  => 'version',
			'short' => 'v'
		);

		$spec  = new ArgSpec($data);
		$name  = $spec->getName();
		$short = $spec->getShortOption();
		$long  = $spec->getLongOption();
		$expectedError = $this->createDefaultError($name, $short, $long);

		$this->assertEquals('version', $name);
		$this->assertEquals('v', $short);
		$this->assertTrue($spec->isShortOption());
	
		$this->assertNull($long);
		$this->assertFalse($spec->isLongOption());
		$this->assertEquals($expectedError, $spec->getErrorText());
		$this->assertNull($spec->getHelpText());
		$this->assertFalse($spec->isHelpText());
		$this->assertFalse($spec->isRequired());
		$this->assertFalse($spec->isParamsAllowed());

		$expectedDelims = $this->getDefaultParamDelimiters();
		$this->assertEquals($expectedDelims, $spec->getParamDelimiters());

		return $data;
	}

	/**
	 * @test
	 * @return
	 */
	public function onlyLongOpt()
	{
		$data = array(
			'name'  => 'version',
			'long' => 'version'
		);

		$spec  = new ArgSpec($data);
		$name  = $spec->getName();
		$short = $spec->getShortOption();
		$long  = $spec->getLongOption();
		$expectedError = $this->createDefaultError($name, $short, $long);

		$this->assertEquals('version', $name);
		$this->assertEquals('version', $long);
		$this->assertTrue($spec->isLongOption());
	
		$this->assertNull($short);
		$this->assertFalse($spec->isShortOption());
		$this->assertEquals($expectedError, $spec->getErrorText());
		$this->assertNull($spec->getHelpText());
		$this->assertFalse($spec->isHelpText());
		$this->assertFalse($spec->isRequired());
		$this->assertFalse($spec->isParamsAllowed());

		return $data;
	}

	/**
	 * @test
	 * @depends	onlyLongOpt
	 * @return	null
	 */
	public function customErrorText(array $data)
	{
		$data['error'] = "my custom error text";
		$spec = new ArgSpec($data);
		$this->assertEquals($data['error'], $spec->getErrorText());
	}

	/**
	 * @test
	 * @depends	onlyLongOpt
	 * @return	null
	 */
	public function helpText(array $data)
	{
		$data['help'] = "my custom help text";
		$spec = new ArgSpec($data);
		$this->assertEquals($data['help'], $spec->getHelpText());
	}

	/**
	 * @test
	 * @depends	onlyLongOpt
	 * @return	null
	 */
	public function isRequired(array $data)
	{
		$data['required'] = true;
		$spec = new ArgSpec($data);
		$this->assertTrue($spec->isRequired());
	}

	/**
	 * @test
	 * @depends	onlyLongOpt
	 * @return	null
	 */
	public function allowParams(array $data)
	{
		$data['allow-params'] = true;
		$spec = new ArgSpec($data);
		$this->assertTrue($spec->isParamsAllowed());
	}

	/**
	 * @test
	 * @depends	onlyLongOpt
	 * @return	null
	 */
	public function paramDelims(array $data)
	{
		$data['param-delims'] = array(':', '|');;
		$spec = new ArgSpec($data);
		$this->assertEquals($data['param-delims'], $spec->getParamDelimiters());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function noNameFailure()
	{
		$data = array(
			'short' => 'v',
			'long'  => 'version',
		);
		$msg = 'argument name must be given using the -(name) key';
		$this->setExpectedException('DomainException', $msg);
		$spec = new ArgSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function nameFailure($name)
	{
		$data = array(
			'name' => $name,
			'short' => 'v',
			'long'  => 'version'
		);

		$msg = 'name of the argument must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = new ArgSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidShort
	 * @return			null
	 */
	public function shortFailure($opt)
	{
		$data = array(
			'name' => 'version',
			'short' => $opt,
			'long'  => 'version'
		);

		$msg = 'short option must be a single character';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = new ArgSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function shortOptNotStringFailure($opt)
	{
		$data = array(
			'name' => 'version',
			'short' => $opt,
			'long'  => 'version'
		);

		$msg = 'short option must be a single character';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = new ArgSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function longOptFailure($opt)
	{
		$data = array(
			'name' => 'version',
			'short' => 'v',
			'long'  => $opt
		);

		$msg = 'long option must be longer than a single character';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = new ArgSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return			null
	 */
	public function helpTextFailure($help)
	{
		$data = array(
			'name' => 'version',
			'short' => 'v',
			'help'  => $help
		);

		$msg = 'help text must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = new ArgSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return			null
	 */
	public function errorTextFailure($error)
	{
		$data = array(
			'name' => 'version',
			'short' => 'v',
			'error'  => $error
		);

		$msg = 'error text must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = new ArgSpec($data);
	}

	/**
	 * @test
	 * @return			null
	 */
	public function paramDelimsNotStringFailure()
	{
		$data = array(
			'name' => 'version',
			'short' => 'v',
			'param-delims'  => array(1,2,3)
		);

		$msg = 'parameter delimiter must be a string';
		$this->setExpectedException('DomainException', $msg);
		$spec = new ArgSpec($data);
	}

	/**
	 * @test
	 * @return			null
	 */
	public function noShortOrLongOptFailure()
	{
		$data = array(
			'name' => 'version',
			'help' => 'some help text'
		);

		$msg  = "-(version) cmust have at least one of the ";
		$msg .= "following options set: -(short,long)";
		$this->setExpectedException('DomainException', $msg);
		$spec = new ArgSpec($data);
	}
}
