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

/**
 * Filters are registered and run by the filter manager which is used by
 * the front controller to perform business logic before and after a 
 * user request is executed in the action controller
 */
interface InterceptFilterInterface
{
	/**
	 * @param	MvcContextInterface	$context
	 * @param	ContextBuilderInterface $builder
	 * @return	null
	 */
	public function apply(MvcContextInterface $context, 
						  ContextBuilderInterface $builder);

    /**
     * @return  InterceptFilterInterface
     */
	public function continueToNextFilter();

    /**
     * @return  InterceptFilterInterface
     */
	public function breakFilterChain();

    /**
     * @return  bool
     */
	public function isBreakChain();

    /**
     * @param   MvcContextInterface $context
     * @return  InterceptFilterInterface
     */
    public function setContextToReplace(MvcContextInterface $context);

	/**
	 * @return MvcContextInterface
	 */		
	public function getContextToReplace();

	/**
	 * @return	bool
	 */
	public function isReplaceContext();

    /**
     * @return  mixed
     */
    public function getCallback();

    /**
     * @param   mixed $func
     * @return  InterceptFilter
     */
    public function setCallback($func);

    /**
     * @return  bool
     */
    public function isCallback();
}
