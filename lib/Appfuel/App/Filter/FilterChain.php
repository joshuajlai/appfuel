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
	Appfuel\Framework\App\Filter\FilterChainInterface,
	Appfuel\Framework\App\Filter\InterceptingFilterInterface;

/**
 * Filter chain is designed to hold filters of a particular type pre or post.
 * It also adds a filter but can not remove one once it is added and it can
 * apply start the filter chain
 */
class FilterChain implements FilterChainInterface
{
	/**
	 * Determines is this is a post or pre filter. We have to so that the 
	 * filter manager can determine which filter chain to this filter in
	 * @var	string
	 */
	protected $type = null;

	/**
	 * The current filter that will be the first filter used
	 * @var InterceptingFilterInterface
	 */
	protected $head = null;

	/**
	 * @param	string	$type	pre|post
	 * @param	InterceptingFilterInterface	$next
	 */
	public function __construct($type)
	{
		$this->setType($type);
	}

	/**
	 * @param	ContextInterface
	 * @return	mixed	Exception | ContextInterface
	 */
	public function apply(ContextInterface $context)
	{
		$head = $this->getHead();
		if (! $head) {
			throw new Exception("Can not apply filter chain no assignments");
		}

		return $head->filter($context);
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
	 * @return	InterceptingFilter
	 */
	public function setHead(InterceptingFilterInterface $filter)
	{
		$this->head = $filter;
		return $this;
	}
	
	/**
	 * This will add the filter to head if there is not filter in head. 
	 * Otherwise it will set the filter in head to the next filter of the 
	 * filter passed and use the filter passed as head.
	 *
	 * @return	InterceptingFilterInterface | null when not set
	 */
	public function addFilter(InterceptingFilterInterface $filter)
	{
		$type = $this->getType();
		if ($this->getType() !== $filter->getType()) {
			throw new Exception("Filter given is should be $type");
		}
		$head = $this->getHead();
		if (null === $head) {
			return $this->setHead($filter);
		}

		$filter->setNext($head);
		return $this->setHead($filter);
	}

	/**
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param	string	$type	pre|post
	 * @return	InterceptingFilter
	 */
	public function setType($type)
	{
		if (empty($type) || ! is_string($type)) {
			throw new Exception("type must be a non empty string");		
		}
		$type = strtolower($type);
		if (! in_array($type, array('pre', 'post'))) {
			throw new Exception("type must have a value of -(pre|post)");
		}

		$this->type = $type;
		return $this;
	}
}
