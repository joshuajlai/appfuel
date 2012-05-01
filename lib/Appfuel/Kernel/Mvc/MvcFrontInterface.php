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
 * The front controller is used build the intialize context, run the pre
 * intercepting filters, dispatch to the mv action, handle any errors,
 * run post filters and output the results.
 */
interface MvcFrontInterface
{	
	/**
	 * @return	MvcActionDispatcherInterface
	 */
	public function getDispatcher();

	/**
	 * @return	InterceptChainInterface
	 */
	public function getPreChain();

	/**
	 * @return	InterceptChainInterface
	 */
	public function getPostChain();

	/**
	 *  
	 * @param	string	$strategy	console|ajax|htmlpage
	 * @return	int
	 */
	public function run(MvcContextInterface $context);

    /**
	 * @param	MvcRouteDetailInterface $detail
     * @param   MvcContextInterface     $context
     * @return  MvcContextInterface
     */
    public function runPreFilters(MvcRouteDetailInterface $detail,
                                  MvcContextInterface $context);

	/**
	 * @param	MvcRouteDetailInterface $detail
	 * @param	MvcContextInterface		$context
	 * @return	MvcContextInterface
	 */
	public function runPostFilters(MvcRouteDetailInterface $detail,
								   MvcContextInterface $context);
}
