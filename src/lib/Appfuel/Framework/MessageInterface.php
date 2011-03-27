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
namespace Appfuel\Framework;

/**
 * tba
 */
interface MessageInterface
{
    public function setRoute(RouteInterface $route);
    public function getRoute();
	public function isRoute();
    public function getRequest();
    public function setRequest(RequestInterface $request);
	public function isRequest();
    public function get($key);
    public function add($key, $value);
}
