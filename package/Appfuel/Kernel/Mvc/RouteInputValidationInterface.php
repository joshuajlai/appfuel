<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use DomainException;

/**
 */
interface RouteInputValidationInterface
{
	/**
	 * @return	RouteInputValidation
	 */
	public function throwExceptionOnFailure();

	/**
	 * @return	RouteInputValidation
	 */
	public function ignoreValidationFailure();

	/**
	 * @return bool
	 */
	public function isThrowOnFailure();

	/**
	 * @return	scalar
	 */
	public function getErrorCode();

	/**
	 * @param	scalar	$code
	 * @return	RouteInputValidation
	 */
	public function setErrorCode($code);

	/**
	 * @param	array	$list
	 * @return	RouteInputValidation
	 */
	public function setSpecList(array $list);

	/**
	 * @return	array
	 */
	public function getSpecList();
}
