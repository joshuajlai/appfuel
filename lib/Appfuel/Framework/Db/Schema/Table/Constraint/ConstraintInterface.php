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
namespace Appfuel\Framework\Db\Schema\Constraint;

use Appfuel\Framework\Sql\SqlStringInterface;

/**
 * Functionality used by all constraints.
 */
interface ConstraintInterface extends SqlStringInterface
{
	/**
	 * The sql used to represent the constraint. For example,
	 * not null constraint is NOT NULL, primary key is PRIMARY KEY
	 * 
	 * @return	string
	 */
	public function getSqlPhrase();

}
