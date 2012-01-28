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
	 * Type of filter determines which filter chain it lives in. Pre filters
	 * happen before the mvc action is executed and post filters happen after
	 * @var string 
	 */
	protected $type = 'pre';

	/**
	 * Used by the filter chain to determine if it should continue to the 
	 * next filter
	 * @var bool
	 */
	protected $isNext = true;

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
	public function __construct($type = null, $callback = null)
	{
		if (is_string($type) && strtolower($type) && 'post' === $type) {
			$this->type = 'post';
		}

		if (null !== $callback) {
			$this->setCallback($callback);
		}
	}

	/**
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return	bool
	 */
	public function isPre()
	{
		return 'pre' === $this->type;
	}

	/**
	 * @return	bool
	 */
	public function isPost()
	{
		return 'post' === $this->type;
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
		$this->isNext = true;
		return $this;
	}

	/**
	 * @return	InterceptFilter
	 */
	public function breakFilterChain()
	{
		$this->isNext = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isBreakChain()
	{
		return $this->isNext;
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
		if (isset($result['is-next']) && false === $result['is-next']) {
			$this->breakFilterChain();
		}

		if (isset($result['replace-context']) &&
			$result['replace-context'] instanceof MvcContextInterface) {
			$this->setContextToReplace($result['replace-context']);
		}
	}
}
