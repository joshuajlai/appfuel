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
interface ColumnInterface
{
	/**
	 * Name of the column
	 */
	public function getName();

	/**
	 * @return	DataTypeInterface 
	 */
	public function getDataType();

	/**
	 * @return	bool
	 */
	public function isNullEnabled();

	/**
	 * @return	bool
	 */
	public function isDefaultEnabled();

	/**
	 * @return	mixed
	 */
	public function getDefaultValue();
}
