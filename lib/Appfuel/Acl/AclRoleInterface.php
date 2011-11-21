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

use InvalidArgumentException;

/**
 * The acl role is an immutable value object that defines an authority level
 */
interface AclRoleInterface
{
	/**
	 * @return	string
	 */
	public function getName();

	/**
	 * @return	string
	 */
	public function getCode();

	/**
	 * @return	int
	 */
	public function getPriority();

	/**
	 * @return string
	 */
	public function getDescription();
}
