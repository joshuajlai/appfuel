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
	Appfuel\Console\ArgParser,
	Testfuel\TestCase\BaseTestCase;

class ArgParserTest extends BaseTestCase
{
	/**
	 * @param	array	$data
	 * @return	array
	 */
	public function createResult(array $data = array())
	{
		$result = array(
			'cmd'   => null,
			'long'  => array(),
			'short' => array(),
			'args'  => array()
		);

		return array_merge($result, $data);
	}

	/**
	 * @test
	 * @return	ArgParser
	 */
	public function createArgParser()
	{
		$parser = new ArgParser();
		$this->assertInstanceOf('Appfuel\Console\ArgParserInterface', $parser);
		return $parser;
	}

	/**
	 * @test
	 * @depends	createArgParser
	 * @return	ArgParser
	 */
	public function testEmptyArgs(ArgParser $parser)
	{
		$this->assertEquals($this->createResult(), $parser->parse(array()));
		return $parser;
	}

	/**
	 * A double dash with nothing else is treated as empty
	 *
	 * @test
	 * @depends	testEmptyArgs
	 * @return	ArgParser
	 */
	public function testDoubleDashOnlyArgs(ArgParser $parser)
	{
		$data	  = array('./my-cmd', '--');
		$expected = $this->createResult(array('cmd' => './my-cmd'));
		$this->assertEquals($expected, $parser->parse($data));
		return $parser;
	}

	/**
	 * @test
	 * @depends	testEmptyArgs
	 * @return	ArgParser
	 */
	public function cliCmd(ArgParser $parser)
	{
		$data = array('./my-cmd');
		$expected = $this->createResult(array('cmd' => './my-cmd'));
		$this->assertEquals($expected, $parser->parse($data));
		return $parser;
	}

	/**
	 * @test
	 * @depends	cliCmd
	 * @return	ArgParser
	 */
	public function shortOptAsFlagAlone(ArgParser $parser)
	{
		$data = array('./my-cmd', '-a');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 'short' => array('a' => true)
		));
		$this->assertEquals($expected, $parser->parse($data));

		return $parser;
	}

	/**
	 * @test
	 * @depends	shortOptAsFlagAlone
	 * @return	ArgParser
	 */
	public function shortOptCluster(ArgParser $parser)
	{
		$data = array('./my-cmd', '-abcd');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'short' => array(
				'a' => true,
				'b' => true,
				'c' => true,
				'd' => true
			)
		));
		$this->assertEquals($expected, $parser->parse($data));
		return $parser;
	}

	/**
	 * @test
	 * @depends	shortOptCluster
	 * @return	ArgParser
	 */
	public function shortOptWithValue(ArgParser $parser)
	{
		$data = array('./my-cmd', '-a', 'my-value');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'short' => array(
				'a' => 'my-value',
			)
		));
		$this->assertEquals($expected, $parser->parse($data));
		return $parser;
	}

	/**
	 * @test
	 * @depends	shortOptWithValue
	 * @return	ArgParser
	 */
	public function manyShortOptWithValues(ArgParser $parser)
	{
		$data = array(
			'./my-cmd', 
			'-a', 
			'value-a', 
			'-b', 
			'value-b', 
			'-c',
			'value-c'
		);
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'short' => array(
				'a' => 'value-a',
				'b' => 'value-b',
				'c' => 'value-c'
			)
		));
		$this->assertEquals($expected, $parser->parse($data));
		return $parser;
	}

	/**
	 * @test
	 * @depends	cliCmd
	 * @return	ArgParser
	 */
	public function longOptAsFlagAlone(ArgParser $parser)
	{
		$data = array('./my-cmd', '--long-opt');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'long' => array('long-opt' => true)
		));
		$this->assertEquals($expected, $parser->parse($data));

		return $parser;
	}

	/**
	 * @test
	 * @depends	longOptAsFlagAlone
	 * @return	ArgParser
	 */
	public function longOptAsFlagMany(ArgParser $parser)
	{
		$data = array('./my-cmd', '--opt-a', '--opt-b', '--opt-c');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'long' => array(
				'opt-a' => true,
				'opt-b' => true,
				'opt-c' => true
		)));
		$this->assertEquals($expected, $parser->parse($data));

		return $parser;
	}

	/**
	 * @test
	 * @depends	longOptAsFlagAlone
	 * @return	ArgParser
	 */
	public function longOptWithValueEqual(ArgParser $parser)
	{
		$data = array('./my-cmd', '--opt=value');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'long' => array(
				'opt' => 'value',
		)));
		$this->assertEquals($expected, $parser->parse($data));

		$data = array('./my-cmd', '--opt=value and more values');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'long' => array(
				'opt' => 'value and more values',
		)));
		$this->assertEquals($expected, $parser->parse($data));

		$data = array('./my-cmd', '--opt=');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'long' => array(
				'opt' => null,
		)));
		$this->assertEquals($expected, $parser->parse($data));
		return $parser;
	}

	/**
	 * @test
	 * @depends	longOptWithValueEqual
	 * @return	ArgParser
	 */
	public function longOptWithValueSpace(ArgParser $parser)
	{
		$data = array('./my-cmd', '--opt', 'value');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'long' => array(
				'opt' => 'value',
		)));
		$this->assertEquals($expected, $parser->parse($data));

		$data = array('./my-cmd', '--opt', 'value and more values');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'long' => array(
				'opt' => 'value and more values',
		)));
		$this->assertEquals($expected, $parser->parse($data));

		$data = array('./my-cmd', '--opt', '');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'long' => array(
				'opt' => '',
		)));
		$this->assertEquals($expected, $parser->parse($data));
		return $parser;
	}

	/**
	 * @test
	 * @depends	longOptAsFlagAlone
	 * @return	ArgParser
	 */
	public function manyLongOptions(ArgParser $parser)
	{
		$data = array(
			'./my-cmd', 
			'--opt-a=value-a', 
			'--opt-b',
			'value-b',
			'--opt-c',
			'c option value'
		);
		$expected = $this->createResult(array(
			'cmd' => './my-cmd', 
			'long' => array(
				'opt-a' => 'value-a',
				'opt-b' => 'value-b',
				'opt-c' => 'c option value'
		)));
		$this->assertEquals($expected, $parser->parse($data));
		return $parser;
	}

	/**
	 * @test
	 * @depends	longOptAsFlagAlone
	 * @return	ArgParser
	 */
	public function singleArg(ArgParser $parser)
	{
		$data = array('./my-cmd', 'arg');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd',
			'args' => array('arg')
		));
		$this->assertEquals($expected, $parser->parse($data));
		return $parser;
	}

	/**
	 * @test
	 * @depends	singleArg
	 * @return	ArgParser
	 */
	public function manyArgs(ArgParser $parser)
	{
		$data = array('./my-cmd', 'arg1', 'arg2', 'arg3');
		$expected = $this->createResult(array(
			'cmd' => './my-cmd',
			'args' => array('arg1', 'arg2', 'arg3')
		));
		$this->assertEquals($expected, $parser->parse($data));
		return $parser;
	}

	/**
	 * @test
	 * @depends	singleArg
	 * @return	ArgParser
	 */
	public function shortLongAndArgs(ArgParser $parser)
	{
		$data = array(
			'./my-cmd', 
			'arg1', 
			'-s', 
			'--long',
			'-abc',
			'--long-value=value',
			'-e',
			'value-e',
			'arg2',
			'arg3',
			'--long-other',
			'other-value'
		);
		$expected = $this->createResult(array(
			'cmd' => './my-cmd',
			'short' => array(
				's' => true,
				'a' => true,
				'b' => true,
				'c' => true,
				'e' => 'value-e',
			),
			'long' => array(
				'long' => true,
				'long-value' => 'value',
				'long-other' => 'other-value'
			),
			'args' => array('arg1', 'arg2', 'arg3')
		));
		$this->assertEquals($expected, $parser->parse($data));
		return $parser;
	}
}
