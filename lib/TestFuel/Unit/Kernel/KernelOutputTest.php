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

use Appfuel\Kernel\KernelOutput,
	TestFuel\TestCase\BaseTestCase;

/**
 * The kernel output uses the KernelRegistry::getAppStrategy to determine
 * which output engine to use: console uses the console output and 
 * everything else uses the HttpOutput
 */
class KernelOutputTest extends BaseTestCase
{
	/**
	 * @return	KernelOutputInterface
	 */
	public function testInitialStateNoInputs()
	{
		$output = new KernelOutput();
		$this->assertInstanceOf('Appfuel\Kernel\OutputInterface', $output);
		
		$this->assertInstanceOf(
			'Appfuel\Http\HttpOutput',
			$output->getHttpOutput()
		);

		$this->assertInstanceOf(
			'Appfuel\Console\ConsoleOutput',
			$output->getConsoleOutput()
		);
		return $output;
	}

	/**
	 * @return	 null
	 */
	public function testInjectingHttpOutput()
	{
		$http = $this->getMock('Appfuel\Http\HttpOutputInterface');
		$output = new KernelOutput($http);
		$this->assertSame($http, $output->getHttpOutput());
		$this->assertInstanceOf(
			'Appfuel\Console\ConsoleOutput',
			$output->getConsoleOutput()
		);
	}

	/**
	 * @return	 null
	 */
	public function testInjectingConsoleOutput()
	{
		$console = $this->getMock('Appfuel\Console\ConsoleOutputInterface');
		$output = new KernelOutput(null, $console);
		$this->assertSame($console, $output->getConsoleOutput());
		$this->assertInstanceOf(
			'Appfuel\Http\HttpOutput',
			$output->getHttpOutput()
		);
	}

	/**
	 * @return	 KernelOutput
	 */
	public function testWithInjectedMockOutputs()
	{
		$http = $this->getMock('Appfuel\Http\HttpOutputInterface');
		$console = $this->getMock('Appfuel\Console\ConsoleOutputInterface');
	
		$output = new KernelOutput($http, $console);
		$this->assertSame($console, $output->getConsoleOutput());
		$this->assertSame($http, $output->getHttpOutput());
		return $output;
	}
}
