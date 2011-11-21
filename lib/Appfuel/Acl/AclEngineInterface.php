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
namespace Appfuel\Acl;

/**
 * Checks against a white list of allowed resources for a particular code
 */
Interface AclEngineInterface
{
	/**
	 * @param	string	$code		the AclRole role code
	 * @param	string	$resource	thing you want access to
	 * @return	bool
	 */
	public function isAllowed($code, $resource);
}
