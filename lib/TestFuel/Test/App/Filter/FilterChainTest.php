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
namespace TestFuel\Test\Filter;

use StdClass,
	Example\App\Filter\PreFilterA,
	Example\App\Filter\PreFilterB,
	Example\App\Filter\PreFilterC,
	Example\App\Filter\PreFilterD,
	Example\App\Filter\PostFilterA,
	Example\App\Filter\PostFilterB,
	Example\App\Filter\PostFilterC,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\App\Context\NullContext,
	Appfuel\App\Filter\FilterChain,
	Appfuel\App\Filter\InterceptingFilter,
	Appfuel\Framework\Context\ContextInterface;

/**
 * Controls the usage for all interceptiong filters
 */
class FilterChainTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var FilterManager
	 */
	protected $chain = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->chain = new FilterChain('pre');
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->chain = null;
	}

	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\App\Filter\FilterChainInterface',
			$this->chain
		);
	}

	/**
	 * The head is the first intercept filter used when you apply the 
	 * filter chain
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetHead()
	{
		$this->assertNull($this->chain->getHead());
		
		$filterD = new PreFilterD();
		$this->assertSame(
			$this->chain,
			$this->chain->setHead($filterD),
			'uses a fluent interface'
		);
		$this->assertSame($filterD, $this->chain->getHead());
	}
}
