<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Testfuel\Functional\Validate;

use Appfuel\Validate\Filter\FilterInterface,
	Appfuel\Validate\Filter\FilterSpecInterface;

class MockFilter implements FilterInterface
{
	/**
	 * @return	string
	 */	
	public function filter($raw, array $params = null)
	{}

	public function loadSpec(FilterSpecInterface $spec)
	{
		return $this;
	}
}
