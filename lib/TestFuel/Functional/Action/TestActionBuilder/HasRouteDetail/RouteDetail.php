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
namespace TestFuel\Fake\Action\TestActionBuilder\HasRouteDetail;

use Appfuel\Kernel\Mvc\MvcRouteDetail;

/**
 * The route detail provides specific information about this route
 */
class RouteDetail extends MvcRouteDetail
{
	/**
	 * @return	RouteDetail
	 */
	public function __construct()
	{
		$routeKey = 'action-builder-has-action-detail';
		$isPublic = true;
		$isInternal = false;
		$acl = null;
		$filters = null;
		parent::__construct($routeKey,$isPublic,$isInternal,$acl,$filters); 
	}
}
