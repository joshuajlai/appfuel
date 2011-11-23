<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

/**
 */
interface MvcActionDispatcherInterface
{	
	/**
	 * @return	array
	 */
	public function getActionFactory();

    /**
     * @param   string  $route
     * @param   AppContextInterface $context
     * @return  AppContextInterface
     */
    public function dispatch($route, $strategy, AppContextInterface $context);
}
