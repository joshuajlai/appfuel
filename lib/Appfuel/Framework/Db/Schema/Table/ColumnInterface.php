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

use Appfuel\Framework\Db\Sql\SqlStringInterface;

/**
 * Functionality used by all constraints.
 */
interface ColumnInterface extends SqlStringInterface
{
	/**
	 * @return	string
	 */
	public function getName();

	/**
	 * @param	string	$name	name of the column
	 * @return	ColumnInterface
	 */
	public function setName($name);

	/**
	 * @return	DataTypeInterface
	 */
	public function getDataType();

	/**
	 * @param	DataTypeInterface
	 * @return	ColumnInterface
	 */	
	public function setDataType(DataTypeInterface $dataType);
}
