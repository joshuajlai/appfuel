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
namespace Appfuel\Http;

/**
 * Defines functionality needed to use php header function
 */
interface HttpHeaderFieldInterface
{
	/**
	 * @return	string
	 */
	public function getField();
	
	/**
	 * @return	bool
	 */
	public function isReplace();
	
	/**
	 * @return	null | int
	 */
	public function getCode();

	/**
	 * Should return getFiled
	 * 
	 * @return	string
	 */
	public function __toString();
}
