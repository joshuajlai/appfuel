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
namespace Appfuel\Framework\App\Request;

/**
 * The uri represents the string making the request to the server. All requests
 * must have a uri string that holds at min the route information.
 */
interface UriInterface
{
	public function getUriString();
	public function getPath();
	public function getParams();
	public function getParamString();
}
