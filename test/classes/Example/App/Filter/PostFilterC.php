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

use AppFuel\App\Filter\IntercetingFilter;

/**
 * Used for unit testing this will append to the test-var
 */
class PostilterC extends InterceptingFilter
{
    public function __construct(ContextInterface $context = null)
    {
        parent::__construct('post', $context);
    }

    public function filter(ContextInterface $context)
    {
		$var = $context->get('test-var', '');
		$var .= ':third-post-filterC';
        $context->add('test-var', $var);
        return $this->next($context);
    }
}
