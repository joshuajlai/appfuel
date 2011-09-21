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
namespace Appfuel\App\Filter;

use Appfuel\Framework\Exception,
	Appfuel\Framework\App\ContextInterface,
	Appfuel\Framework\App\Filter\InterceptingFilterInterface;

/**
 * This intercepting filter is responsible for authenticating the current user
 * users request. Private user sessions are created or validated
 */
class AuthFilter extends AbstractFilter implements InterceptingFilterInterface
{
	/**
	 * @return	AuthFilter
	 */
	public function __construct(InterceptingFilterInterface $next = null)
	{
		parent::__construct('pre', $next);
	}

	public function filter(ContextInterface $context)
	{
		// do some stuff
		
		if ($this->isNext()) {
			return $context;
		}
		
		return $this->getNext()
					->filter($context);
	}
}	
