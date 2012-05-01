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
namespace Appfuel\Framework\Db\Constraint;

/**
 * Functionality used by all constraints.
 */
interface ConstraintInterface
{
	/**
	 * The sql used to represent the constraint. For example,
	 * not null constraint is NOT NULL, primary key is PRIMARY KEY
	 * 
	 * @return	string
	 */
	public function getSqlPhrase();

	/**
	 * Generates a sql string that represents the constraint
	 * 
	 * @return	string
	 */
	public function buildSql();

    /**
     * @return  ConstraintInterface
     */
    public function enableUpperCase();

    /**
     * @return  ConstraintInterface
     */
    public function disableUpperCase();

    /**
     * @return  bool
     */
    public function isUpperCase();

	/**
	 * Allow the constraint to be used in the same context as a string
	 * 
	 * @return	string
	 */
	public function __toString();
}
