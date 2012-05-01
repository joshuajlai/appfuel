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
 * Value object used to represent an http status. The code and text should
 * be mapped so that all that is needed is the constructor to except the
 * immutable input. You could implement public setters to do this but appfuel
 * prefers small immutable value objects (reduces side effects)
 */
interface HttpStatusInterface
{
	/**
	 * @return	string
	 */
	public function getCode();
	
	/**
	 * @return	bool
	 */
	public function getText();

	/**
	 * @return	string
	 */
	public function __toString();
}
