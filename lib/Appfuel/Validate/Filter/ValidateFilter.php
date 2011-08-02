<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Validate\Filter;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Validate\Filter\FilterInterface;

/**
 * Create the filter from the name given. In this case the 
 */
abstract class ValidateFilter implements FilterInterface
{
	/**
	 * A string not likely to occur in everyday use for values, this will
	 * indicate a filter failure
	 *
	 * @var string
	 */
	protected $failureToken = '__AF_VALIDATE_FILTER_FAILURE__';

	/**
	 * @return	string
	 */	
	public function failedFilterToken()
	{
		return $this->failureToken;
	}
}
