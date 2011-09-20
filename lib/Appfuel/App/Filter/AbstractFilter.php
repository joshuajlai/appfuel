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
	Appfuel\Framework\App\Context\ContextInterface,
	Appfuel\Framework\App\Filter\InterceptingFilterInterface;

/**
 * This intercepting filter pattern has been modified. The knowledge of the
 * the next filter has been offloaded onto the filter manager.
 */
abstract class AbstractFilter
{
	/**
	 * Determines is this is a post or pre filter. We have to so that the 
	 * filter manager can determine which filter chain to this filter in
	 * @var	string
	 */
	protected $type = null;

	/**
	 * The next intercepting filter to use after our successful filter
	 * @var InterceptingFilterInterface
	 */
	protected $next = null;

	/**
	 * @param	string	$type	pre|post
	 * @param	InterceptingFilterInterface	$next
	 */
	public function __construct($type, InterceptingFilterInterface $next = null)
	{
		$this->setType($type);
		if (null !== $next) {
			$this->setNext($next);
		}
	}

	/**
	 * @param	InterceptingFilterInterface	$filter
	 * @return	InterceptingFilter
	 */
	public function setNext(InterceptingFilterInterface $filter)
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
		return $this->next instanceof InterceptingFilterInterface;
	}

	/**
	 * @param	ContextInterface	$context
	 * @return	ContextInterface | Appfuel\Framework\Exception on failure
	 */ 
	public function next(ContextInterface $context)
	{
		if (! $this->isNext()) {
			return $context;
		}

		$next = $this->getNext();
		return $next->filter($context);
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
