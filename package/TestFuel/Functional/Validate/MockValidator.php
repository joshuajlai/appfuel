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

use Appfuel\Validate\ValidatorInterface,
	Appfuel\Validate\CoordinatorInterface;

/**
 * Used only in testing to prove this object can be created and used by
 * the ValidationManager
 */
class MockValidator implements ValidatorInterface
{
	/**
	 * @param	mixed	$raw	data used to validate with filters
	 * @return	bool
	 */
	public function isValid(CoordinatorInterface $coord)
	{
		return true;
	}
}
