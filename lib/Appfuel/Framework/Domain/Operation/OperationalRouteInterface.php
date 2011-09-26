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
namespace Appfuel\Framework\Domain\Operation;

use Appfuel\Framework\Domain\Action\ActionDomainInterface;

/**
 * The operational route is the binding of an operation and a controller.
 * Used by the front controller to dispatch the request, the front controller
 * looks for the ControllerNamespace, access policy, default format and 
 * request types in the OperationalRoute.
 */
interface OperationalRouteInterface
{
    /**
     * @param   string
     */
    public function getOperation();

    /**
     * @param   OperationInterface  $op
     * @return  OperationalRoute
     */
    public function setOperation(OperationDomainInterface $op);

    /**
     * @param   string
     */
    public function getAction();

    /**
     * @param   OperationInterface  $op
     * @return  OperationalRoute
     */
    public function setAction(ActionDomainInterface $op);


    /**
     * @return  string
     */
    public function getAccessPolicy();

    /**
     * @param   string  $level  either public | private
     * @return  OperationalRoute
     */
    public function setAccessPolicy($level);

    /**
     * @param   string
     */
    public function getRouteString();

    /**
     * @param   string  $route
     * @return  OperationalRoute
     */
    public function setRouteString($route);

    /**
     * Add a single filter to the filter list and mark the filters as dirty
     * 
     * @throws  Appfuel\Framework\Exception
     * @param   string  $filter
     * @return  null
     */
    public function addFilter($filter, $type);

	/**
	 * Loads both pre and post filters form this array
	 * 
	 * @param	array	$filters
	 * @return	OperationalRoute
	 */
	public function setFilters(array $filters);

    /**
     * @return  array
     */
    public function getPreFilters();

    /**
     * @return  array
     */
    public function getPostFilters();
}
