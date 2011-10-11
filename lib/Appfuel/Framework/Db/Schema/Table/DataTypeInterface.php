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
 * Decribes the functionality of Integer datatypes
 */
interface DataTypeInterface extends SqlStringInterface
{
	/**
	 * Each datatype has its own validator used to ensure the value in 
	 * column corresponds to the datatype of that column.
	 *
	 * @return	string
	 */
	public function getValidatorName();
}
