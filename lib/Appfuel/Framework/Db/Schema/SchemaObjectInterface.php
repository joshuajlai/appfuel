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
namespace Appfuel\Framework\Db\Schema;


/**
 * Allows schema objects to expose the generic concept of attributes
 */
interface SchemaObjectInterface
{
	/**
	 * @param	string	$name		name of the attribute
	 * @param	mixed	$default	default value to used when not found
	 * @return	mixed
	 */
	public function getAttribute($name, $default = null);
	
	/**
	 * @param	string	$name	name of the attribute
	 * @return	bool
	 */
	public function isAttribute($name);
}
