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
namespace TestFuel\Provider;

/**
 * Encapsulates on the general cases for getting sets string values to be
 * used with phpunits dataProvider
 */
interface StringProviderInterface
{
	/**
	 * @return	array
	 */
	public function provideAllStrings();

	/**
	 * @return	array
	 */
	public function provideNumericStrings();

	/**
	 * @return	array
	 */
	public function providEmptyStrings();

	/**
	 * @return	array
	 */
	public function provideNonEmptyStrings($isNumeric = true);

	/**
	 * @return	array
	 */
	public function provideInvalidStrings($isNull = true);
}
