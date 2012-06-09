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
/**
 * Specification used to control the automation of input validation by
 * the dispatcher
 */
class RouteInputValidation implements RouteInputValidationInterface
{
	/**
	 * Flag used to tell the dispatcher to ignore input validation
	 * @var bool
	 */
	protected $isInputValidation = true;

	/**
	 * Flag used to determine if the dispatcher should throw an exception
	 * when a failure is detected by input validation
	 * @var	 bool
	 */
	protected $isThrowOnFailure = true;

	/**
	 * Error code used by the dispatcher when throwOnFailure is true
	 * @var bool
	 */
	protected $errorCode = 500;

	/**
	 * List of validation specifications used for input validation
	 * @var	array
	 */
	protected $specList = array();

	/**
	 * @return	RouteInputValidation
	 */
	public function ignoreInputValidation()
	{
		$this->isInputValidation = false;
		return $this;
	}

	/**
	 * @return	RouteInputValidation
	 */
	public function validateInput()
	{
		$this->isInputValidation = true;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isInputValidation()
	{
		return $this->isInputValidation;
	}

	/**
	 * @return	RouteInputValidation
	 */
	public function throwExceptionOnFailure()
	{
		$this->isThrowOnFailure = true;
		return $this;
	}

	/**
	 * @return	RouteInputValidation
	 */
	public function ignoreValidationFailure()
	{
		$this->isThrowOnFailure = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isThrowOnFailure()
	{
		return $this->isThrowOnFailure;
	}

	/**
	 * @return	scalar
	 */
	public function getErrorCode()
	{
		return $this->errorCode;
	}

	/**
	 * @param	scalar	$code
	 * @return	RouteInputValidation
	 */
	public function setErrorCode($code)
	{
		if (null !== $code && ! is_scalar($code)) {
			$err = 'error code must be a scalar value or null';
			throw new DomainException($err);	
		}

		$this->errorCode = $code;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isSpecList()
	{
		return ! empty($this->specList);
	}

	/**
	 * @param	array	$list
	 * @return	RouteInputValidation
	 */
	public function setSpecList(array $list)
	{
		$this->specList = $list;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getSpecList()
	{
		return $this->specList;
	}
}
