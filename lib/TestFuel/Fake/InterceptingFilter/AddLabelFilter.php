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

use Appfuel\Kernel\Mvc\AppContextInterface,
	Appfuel\Kernel\Mvc\Filter\AbstractFilter,
	Appfuel\Kernel\Mvc\Filter\InterceptingFilterInterface;

/**
 * This will add the label 'my-assignment' to the view with the value
 * 'value 1 2 3'
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
	public function filter(AppContextInterface $context)
	{
		$view = $context->getView();
		$view->assign('my-assignment', 'value 1 2 3');
	}
}
