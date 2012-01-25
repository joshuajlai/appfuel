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


use Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 * Filters are registered and run by the filter manager which is used by
 * the front controller to perform business logic before and after a 
 * user request is executed in the action controller
 */
interface InterceptFilterInterface
{
	/**
	 * Perform an concrete business logic on the context given. Return
	 * true when everything when well and you don't need to replace the 
	 * context. Return the context if you need to replace the existing context
	 * with a new one. Return false for an error and make sure to set the error
	 *
	 * @param	ContextInterface	$context
	 * @return	mixed	bool | ContextInterface
	 */
	public function filter(MvcContextInterface $context);

    /**
     * @param   InterceptingFilterInterface $filter
     * @return  InterceptingFilter
     */
    public function setNext(InterceptFilterInterface $filter);

    /**
     * @return  InterceptingFilterInterface | null when not set
     */
    public function getNext();

    /**
     * @return  bool
     */
    public function isNext();

    /**
     * @param   AppContextInterface $context
     * @return  AppContextInterface
     */
    public function next(MvcContextInterface $context);
}
