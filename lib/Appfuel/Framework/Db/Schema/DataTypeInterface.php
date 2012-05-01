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
 * Describes a database schema's datatype though an agnostic interface
 * removing version and sql differences. This interface is ment to used by
 * database vendor specific code because it does not generate sql itself.
 */
interface DataTypeInterface extends SchemaObjectInterface
{
	/**
	 * Name of the datatype 
	 */
	public function getName();

	/**
	 * The type modified is an appfuel convention used to decribe an attribute
	 * that can have different meaning across datatypes and databases. 
	 * Basically it is whatever follows the attribute name in parentheses.
	 * Examples		ENUM('a', 'b', 'c')  type modifer is 'a', 'b', 'c'
	 *				INT(6)				 type modified is 6
	 *				VARCHAR(128)		 type modified is 128
	 *				INT					 no type modifier
	 *
	 * @return	mixed  array|string|null when not set
	 */
	public function getTypeModifier();

	/**
	 * @return	bool
	 */
	public function isTypeModifier();
}
