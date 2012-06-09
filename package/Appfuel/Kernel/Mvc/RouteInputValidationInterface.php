<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Mvc;

use DomainException;

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
	 * @return bool
	 */
	public function isSpecList();

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
