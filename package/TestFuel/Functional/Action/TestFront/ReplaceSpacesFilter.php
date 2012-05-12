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

use Appfuel\View\ViewTemplateInterface,
	Appfuel\Kernel\Mvc\MvcContextInterface,
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
	public function filter(MvcContextInterface $context)
	{
		$view = $context->getView();
		if (! ($view instanceof ViewTemplateInterface)) {
			$this->next($context);;
		}

		$value = $view->get('my-assignment', '');
		$value = str_replace(' ', ':', $value);
		$view->assign('my-assignment', $value);
		$this->next($context);
	}
}
