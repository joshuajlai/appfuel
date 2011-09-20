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
namespace Example\App\Filter;

use AppFuel\App\Filter\AbstractFilter,
	Appfuel\Framework\App\Context\ContextInterface
    Appfuel\Framework\App\Filter\InterceptingFilterInterface;

/**
 * Designed for unit tests
 */
class PreFilterB extends AbstractFilter implements InterceptingFilterInterface
{
    public function __construct(ContextInterface $context = null)
    {
        parent::__construct('pre', $context);
    }

    public function filter(ContextInterface $context)
    {
		$var = $context->get('test-var', '');
		$var .= ':second-pre-filterB';
        $context->add('test-var', $var);
        return $this->next($context);
    }
}
