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

use Appfuel\View\ViewTemplateInterface,
	Appfuel\Kernel\Mvc\ContextInterface,
	Appfuel\Kernel\Mvc\Filter\AbstractFilter,
	Appfuel\Kernel\Mvc\Filter\InterceptingFilterInterface;

/**
 * The controller will assign the pre filters label into the view where we
 * replace spaces with colons
 */
class ReplaceSpacesFilter 
	extends AbstractFilter implements InterceptingFilterInterface
{
	/**
	 * @param	InterceptingFilterInterface $next
	 * @return	AddLabelFilter
	 */
	public function __construct(InterceptingFilterInterface $next = null)
	{
		parent::__construct('post', $next);
	}

	/**
	 * @param	ContextInterface $context
	 * @return	null
	 */
	public function filter(ContextInterface $context)
	{
		$view = $context->get('app-view', false);
		if (false === $view || ! ($view instanceof ViewTemplateInterface)) {
			return;
		}

		$value = $view->getAssigned('test-filter-label', '');
		$value = str_replace(' ', ':', $value);
		$view->assign('test-filter-label', $value);
	}
}
