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
namespace Appfuel\Kernel\Mvc\Filter;

use InvalidArgumentException,
	Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\ContextBuilder,
	Appfuel\Kernel\Mvc\ContextBuilderInterface;

/**
 * This intercepting filter pattern has been modified. The knowledge of the
 * the next filter has been offloaded onto the filter manager.
 */
class GeneralIntercept
{
	/**
	 * The next intercepting filter to use after our successful filter
	 * @var InterceptingFilterInterface
	 */
	protected $next = null;

	public $context = null;

	/**
	 * @param	InterceptingFilterInterface	$filter
	 * @return	InterceptingFilter
	 */
	public function setNext(InterceptFilterInterface $filter)
	{
		$this->next = $filter;
		return $this;
	}
	
	/**
	 * @return	InterceptingFilterInterface | null when not set
	 */
	public function getNext()
	{
		return $this->next;
	}

	/**
	 * @return	bool
	 */
	public function isNext()
	{
		return $this->next instanceof InterceptFilterInterface;
	}

	/**
	 * @param	AppContextInterface	$context
	 * @return	AppContextInterface
	 */ 
	public function next(MvcContextInterface $context)
	{
		if (! $this->isNext()) {
			return $context;
		}

		$next = $this->getNext();
		if ($this->context  instanceof MvcContextInterface) {
			$context = $this->context;
		}

		return $next->filter($context);
	}
}
