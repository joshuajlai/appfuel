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

/**
 * Validator used to run filters against a single field
 */
interface FieldValidatorInterface
{
	/**
	 * Return the name of the field we are validating against
	 *
	 * @return	string
	 */
	public function getField();
}
