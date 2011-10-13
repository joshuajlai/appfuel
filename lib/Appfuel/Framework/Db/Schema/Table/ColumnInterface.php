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

use Appfuel\Framework\Db\Sql\SqlStringInterface,
	Appfuel\Framework\Db\Schema\Table\Constraint\ConstraintInterface;

/**
 * Functionality used by all constraints.
 */
interface ColumnInterface extends SqlStringInterface
{
	/**
	 * Enforce immutable members for name and data type
	 * public function setDataType(DataTypeInterface $dataType);
	 *
	 * @param	string	$name	name of the column
	 * @param	DataTypeInterface $dataType		
	 * @return	ColumnInterface
	 */
	public function __construct($name, 
								DataTypeInterface $dataType,
								ConstraintInterface $notNull = null,
                                ConstraintInterface $default = null);

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
	

}
