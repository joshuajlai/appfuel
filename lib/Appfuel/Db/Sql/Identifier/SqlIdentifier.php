<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Db\Sql\Identifier;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Expr\BasicExpr;

/**
 * Simple expression that validates the string given follows the rules for
 * sql92 identifiers.
 */
class SqlIdentifier extends BasicExpr
{
	protected $reserved = null;

	/**
     * @param   string   $name
     * @return  File
     */
    public function __construct($name)
    {
		$this->reserved = new SqlReservedWords();
		parent::__construct($name);
    }

	/**
	 * Permently disable parentheses because they should not be used in this
	 * context
	 * 
	 * @return false
	 */
	public function isParentheses()
	{
		return false;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$name
	 * @return	null
	 */
	protected function setOperand($name)
	{
        if (! $this->isValid($name)) {
            $err = 'identifier is not a valid sql92 identifier';
            throw new Exception($err);
        }

        $this->operand = $name;
	}

	/**
	 * Override to replace with rules for sql 92. Must be a non empty string
	 * less than 128 characters that is not a reserved word or if the first
	 * and last char is a " then any word can be used between the double 
	 * quotes
	 *
	 * @param	string	$name
	 * @return	bool
	 */ 
	protected function isValid($name)
	{
		if (empty($name) || ! is_string($name)) {
			return false;
		}

		$len = strlen($name);
		if ($len > 128) {
			return false;
		}
	
		/* account for 0 index */
		$len -= 1;
		$first = $name{0};
		$last  = $name{$len};

		if ('"' === $first && '"' === $last) {
			return true;
		}

		if ($this->reserved->isReserved($name)) {
			return false;
		}

		return true;
	}
}
