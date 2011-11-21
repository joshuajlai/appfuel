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
	Appfuel\Kernel\Mvc\ContextInterface;

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
	 * @return	bool
	 */
	public function hasFilters()
	{
		return $this->head instanceof InterceptingFilterInterface;
	}

	/**
	 * @param	ContextInterface
	 * @return	mixed	Exception | ContextInterface
	 */
	public function apply(ContextInterface $context)
	{
		$head = $this->getHead();
		if (! $head) {
			$err = "Can not apply filter chain no assignments";
			throw new RunTimeException($err);
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
	 * @throws	RunTimeException
	 * @param	InterceptingFilterInterface	$filter
	 * @return	InterceptingFilter
	 */
	public function setHead(InterceptingFilterInterface $filter)
	{
		$this->typeCheck($filter);

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
	public function addFilter(InterceptingFilterInterface $filter)
	{
		$this->typeCheck($filter);

		$head = $this->getHead();
		if (null !== $head) {
			$filter->setNext($head);
		}

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
			throw new InvalidArgumentException(
				"type must be a non empty string"
			);		
		}
		$type = strtolower($type);
		if (! in_array($type, array('pre', 'post'))) {
			throw new InvalidArgumentException(
				"type must have a value of -(pre|post)"
			);
		}

		$this->type = $type;
		return $this;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	InterceptingFilterInterface
	 * @return	null
	 */
	protected function typeCheck(InterceptingFilterInterface $filter)
	{
		$chainType  = $this->getType();
		$filterType = $filter->getType();
		if ($chainType !== $filterType) {
			$err  = "addFilter failed: Filter type give is -($filterType) ";
			$err .= "but does not match chain type -($chainType)";
			throw new RunTimeException($err);
		}
	}
}
