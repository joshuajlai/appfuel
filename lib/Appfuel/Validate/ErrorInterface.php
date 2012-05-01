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
namespace Appfuel\Validate;

use Countable, 
	Iterator;

/**
 */
interface ErrorInterface extends Countable, Iterator
{
    /**
     * @return array
     */
    public function getErrors();

	/**
	 * @return	string
	 */
	public function getField();

    /**
     * @param   string   $msg
     * @return  ErrorInterface
     */
    public function add($msg);

	/**
	 * Determine how these errors in the context of a string
	 * 
	 * @return	string
	 */
	public function __toString();

}
