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
namespace TestFuel\Fake\InterceptingFilter;

use Appfuel\Kernel\Mvc\ContextInterface,
	Appfuel\Kernel\Mvc\Filter\AbstractFilter,
	Appfuel\Kernel\Mvc\Filter\InterceptingFilterInterface;

/**
 * This will add the label 'test-filter-label' to the context with the value
 * 'value-1'
 */
class AddLabelFilter 
	extends AbstractFilter implements InterceptingFilterInterface
{
	/**
	 * @param	InterceptingFilterInterface $next
	 * @return	AddLabelFilter
	 */
	public function __construct(InterceptingFilterInterface $next = null)
	{
		parent::__construct('pre', $next);
	}

	/**
	 * @param	ContextInterface $context
	 * @return	null
	 */
	public function filter(ContextInterface $context)
	{
		$context->add('test-filter-label', 'value 1 2 3');
	}
}
