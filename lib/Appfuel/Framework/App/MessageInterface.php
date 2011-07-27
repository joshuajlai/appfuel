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


use Appfuel\Framework\DataStructure\DictionaryInterface,
	Appfuel\Framework\App\Route\RouteInterface,
	Appfuel\Framework\App\Request\RequestInterface;

/**
 *
 */
interface MessageInterface extends DictionaryInterface
{
	public function getRoute();
	public function setRoute(RouteInterface $route);
	public function isRoute();

	public function getRequest();
	public function setRequest(RequestInterface $request);
	public function isRequest();

	public function getResponseType();
	public function setResponseType($type);
	public function calculateResponseType(RequestInterface $reqest, 
										  RouteInterface   $route);

	public function getError();
	public function setError($text);
	public function isError();
	public function clearError();
}
