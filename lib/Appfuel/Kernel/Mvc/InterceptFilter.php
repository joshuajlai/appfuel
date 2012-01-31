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
namespace Appfuel\Kernel\Mvc;

use Closure,
	InvalidArgumentException;

/**
 * This intercepting filter pattern has been modified. The knowledge of the
 * the next filter has been offloaded onto the filter manager.
 */
class InterceptFilter implements InterceptFilterInterface
{
	/**
	 * Used by the filter chain to determine if it should continue to the 
	 * next filter
	 * @var bool
	 */
	protected $isBreakChain = false;

	/**
	 * Used by the filter chain to break the reference of the context passed
	 * in and replace it with this one. Once a replacement exists all other
	 * filters after this one will get the replaced context.
	 * @var	MvcContextInterface
	 */
	protected $context = null;

	/**
	 * The callback is applied when filter has been called by the filter chain.
	 * This is optional you can also extend this class and override the filter
	 * functionality
	 * @var mixed
	 */	
	protected $callback = null;

	/**
	 * @param	string	$type
	 * @return	InterceptFilter
	 */
	public function __construct($callback = null)
	{
		if (null !== $callback) {
			$this->setCallback($callback);
		}
	}

	/**
	 * @return	InterceptFilter
	 */
	public function markAsPostFilter()
	{
		$this->type = 'post';
		return $this;
	}

	/**
	 * @return	InterceptFilter
	 */
	public function markAsPreFilter()
	{
		$this->type = 'pre';
		return $this;
	}

	/**
	 * @return	InterceptFilter
	 */
	public function continueToNextFilter()
	{
		$this->isBreakChain = false;
		return $this;
	}

	/**
	 * @return	InterceptFilter
	 */
	public function breakFilterChain()
	{
		$this->isBreakChain = true;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isBreakChain()
	{
		return $this->isBreakChain;
	}

	/**
	 * @return	MvcContextInterface
	 */
	public function getContextToReplace()
	{
		return $this->context;
	}

	/**
	 * @param	MvcContextInterface
	 * @return	InterceptFilter
	 */
	public function setContextToReplace(MvcContextInterface $context)
	{
		$this->context = $context;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isReplaceContext()
	{
		return $this->context instanceof MvcContextInterface;
	}

	/**
	 * @return	mixed
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * @param	mixed $func
	 * @return	InterceptFilter
	 */
	public function setCallback($func)
	{
		if (! ($func instanceof Closure) &&
			! is_callable($func)) {
			$err = 'callback must be a closure or callable';
			throw new InvalidArgumentException($err);
		}

		/* 
		 * if you are a callback the method must be 'filter'
		 */
		if (is_array($func) && 
			isset($func[0]) && is_object($func[0]) &&
			isset($func[1]) && 'filter' !== $func[1]) {
			$err = 'callback method must be called -(filter)';
			throw new InvalidArgumentException($err);
		}

		$this->callback = $func;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isCallback()
	{
		return is_callable($this->callback);
	}

	/**
	 * @param	MvcContextInterface $context
	 * @param	ContextBuilderInterface $builder
	 * @return	null
	 */
	public function apply(MvcContextInterface $context, 
						  ContextBuilderInterface $builder)
	{
		if (! $this->isCallback()) {
			return;
		}

		$filter = $this->getCallback();
		if ($filter instanceof Closure) {
			$result = $filter($context, $builder);
		}
		else {
			$result = call_user_func($filter, $context, $builder);
		}

		$this->continueToNextFilter();
		$key = 'is-break-chain';
		if (isset($result[$key]) && true === $result[$key]) {
			$this->breakFilterChain();
		}

		$key = 'replace-context';
		if (isset($result[$key]) &&
			$result[$key] instanceof MvcContextInterface) {
			$this->setContextToReplace($result[$key]);
		}
	}
}
