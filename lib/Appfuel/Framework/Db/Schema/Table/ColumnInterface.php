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
namespace Appfuel\Framework\Db\Schema\Table;

/**
 * Functionality used by columns
 */
interface ColumnInterface
{
	/**
	 * @return	string
	 */
	public function getName();

	/**
	 * @return	DataTypeInterface
	 */
	public function getDataType();

	/**
	 * @return	bool
	 */
	public function isNullAllowed();

	/**
	 * @return	bool
	 */
	public function isDefaultValue();

	/**
	 * @return	scalar
	 */
	public function getDefaultValue();	

}
