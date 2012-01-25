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

use	RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 * Filter chain is designed to hold filters of a particular type pre or post.
 * It also adds a filter but can not remove one once it is added and it can
 * apply start the filter chain
 */
class FilterChain implements FilterChainInterface
{
	/**
	 * The current filter that will be the first filter used
	 * @var InterceptingFilterInterface
	 */
	protected $head = null;

	/**
	 * @param	InterceptingFilterInterface	$filter
	 * @return	FilterChain
	 */
	public function __construct(InterceptFilterInterface $filter = null)
	{
		if (null !== $filter) {
			$this->setHead($filter);
		}
	}

	/**
	 * @return	bool
	 */
	public function hasFilters()
	{
		return $this->head instanceof InterceptFilterInterface;
	}

	/**
	 * @throws	RunTimeException
	 * @param	AppContextInterface
	 * @return	mixed	Exception | ContextInterface
	 */
	public function apply(MvcContextInterface $context)
	{
		$head = $this->getHead();
		if (! $head) {
			$err = "Can not apply filter chain no assignments";
			throw new RunTimeException($err);
		}

		$result = $head->filter($context);
		if (! $result instanceof MvcContextInterface) {
			echo "<pre>", print_r($result, 1), "</pre>";exit;
		}

		return $result;
	}

	/**
	 * @return	InterceptingFilterInterface | null when not set
	 */
	public function getHead()
	{
		return $this->head;
	}

	/**
	 * @param	InterceptingFilterInterface	$filter
	 * @return	FilterChain
	 */
	public function setHead(InterceptFilterInterface $filter)
	{
		$this->head = $filter;
		return $this;
	}
	
	/**
	 * This will add the filter to head if there is not filter in head. 
	 * Otherwise it will set the filter in head to the next filter of the 
	 * filter passed and use the filter passed as head.
	 *
	 * @throws	RunTimeException
	 * @return	InterceptingFilterInterface | null when not set
	 */
	public function addFilter(InterceptFilterInterface $filter)
	{
		$head = $this->getHead();
		if ($head) {
			$filter->setNext($head);
		}

		return $this->setHead($filter);
	}
}
