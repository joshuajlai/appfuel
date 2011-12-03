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
namespace TestFuel\Fake\Action\TestFront;

use Appfuel\Kernel\Mvc\AppContextInterface,
	Appfuel\Kernel\Mvc\Filter\AbstractFilter,
	Appfuel\Kernel\Mvc\Filter\InterceptingFilterInterface;

/**
 * This filter work with the results of the AddLabelFilter and prepends	
 * the text '4 5 6 ' to the value of label 'test-filter-label'
 */
class PrependLabelFilter 
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
		$value = $view->getAssigned('my-assignment', false);
		if (false === $value) {
			return $this->next($context);
		}

		$value = '4 5 6 ' . $value;
		$view->assign('my-assignment', $value);
		$this->next($context);
	}
}
