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
namespace Appfuel\Log;

/**
 * Defines a priority for a given log entry. Should be a value object
 */
interface LogPriorityInterface
{
	/**
	 * @return	scalar
	 */
	public function getLevel();

	/**
	 * @return	string
	 */
	public function __toString();
}
