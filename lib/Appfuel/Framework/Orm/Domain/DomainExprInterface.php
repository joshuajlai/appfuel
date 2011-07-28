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
namespace Appfuel\Framework\Orm\Repository;

use Appfuel\Framework\Expr\BinaryExprInterface;

/**
 * Domain Expressions are used by the repository to parse a single string
 * expressing a basic expression about a domain into a known datastructure.
 */
interface DomainExprInterface extends BinaryExprInterface
{
	/**
	 * Returns the domain key used in the expression
	 * @return	string
	 */
	public function getDomain();

	/**
	 * Returns the domain member used in the expression
	 * @return string
	 */
	public function getMember();

	/**
	 * Returns the value of the expression
	 * @return string
	 */
	public function getValue();

}
