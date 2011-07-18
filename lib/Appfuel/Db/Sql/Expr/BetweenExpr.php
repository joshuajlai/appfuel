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
namespace Appfuel\Db\Sql\Expr;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Sql\Expr\ExprInterface;

/**
 * Simple expression designed to old objects that support to string
 */
class BetweenExpr extends AndExpr
{
	/**
	 * Target expression for which the between is applied
	 * @var ExprInterface
	 */
	protected $target = null;

	/**
     * @param   string   $operand
     * @return  File
     */
    public function __construct(ExprInterface $target,
								ExprInterface $lexpr, 
								ExprInterface $rexpr,
								$isParentheses = false)
    {
		$this->target = $target;	
		parent::__construct($lexpr, $rexpr, $isParentheses);
    }


	public function doBuild()
	{	
		$str = ' BETWEEN ' . parent::doBuild();
		return $this->convertToString($this->target) . $str;
	}
}
