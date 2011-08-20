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
namespace Appfuel\Framework\App;


use Appfuel\Framework\App\ContextInterface;

/**
 * Filters are registered and run by the filter manager which is used by
 * the front controller to perform business logic before and after a 
 * user request is executed in the action controller
 */
interface FilterItemInterface
{
	/**
	 * Used to determine if the filter experienced any errors
	 * 
	 * @return	bool
	 */
	public function isError();
	
	/**
	 * Perform an concrete business logic on the context given. Return
	 * true when everything when well and you don't need to replace the 
	 * context. Return the context if you need to replace the existing context
	 * with a new one. Return false for an error and make sure to set the error
	 *
	 * @param	ContextInterface	$context
	 * @return	mixed	bool | ContextInterface
	 */
	public function filter(ContextInterface $context);
	
	/**
	 * Object used to describe the error the filter encountered 
	 * 
	 * @return	Error
	 */
	public function getError();
}
