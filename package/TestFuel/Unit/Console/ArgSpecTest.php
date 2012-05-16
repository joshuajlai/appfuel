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

/**
 */
class ArgSpecTest extends BaseTestCase
{
	/**
	 * @test
	 * @return	null
	 */
	public function argSpecNameShortOpt()
	{
		$data = array(
			'name'  => 'version',
			'short' => 'v'
		);

		$spec  = new ArgSpec($data);
		$name  = $spec->getName();
		$short = $spec->getShortOption();
		$long  = $spec->getLongOption();

		$expectedError  = "cli arg specification failed for -($name): ";
		$expectedError .= "short option: -($short) long option: -($long)";
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
	
	}
}
