<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Validate;

interface ValidationHandlerInterface
{
	/**
	 * @return	CoordinatorInterface
	 */
	public function getCoordinator();

	/**
	 * @param	CoordinatorInterface
	 * @return	ValidationHandlerInterface
	 */
	public function setCoordinator(CoordinatorInterface $coord);

	/**
	 * @param	FieldSpecInterface	$spec
	 * @return	ValidationHandlerInterface
	 */
	public function loadSpec(FieldSpecInterface $spec);

	/**
	 * @param	ValidatorInterface	$validator
	 * @return	ValidationHandlerInterface
	 */
	public function addValidator(ValidatorInterface $validator);

	/**
	 * @return	array
	 */
	public function getValidators();

	/**
	 * @return	ValidationHandlerInterface
	 */
	public function clearValidators();

	/**
	 * @return	bool
	 */
	public function isError();

	/**
	 * @return array
	 */
	public function getErrorStack();

	/**
	 * @return	ValidationHandlerInterface
	 */
	public function clearErrors();

	/**
	 * @return	array
	 */
	public function getAllClean();

	/**
	 * @return	mixed
	 */
	public function getClean($field, $default = null);

	/**
	 * @return	ValidationHandlerInterface
	 */
	public function clearClean();

	/**
	 * @param	mixed	$raw	data used to validate with filters
	 * @return	bool
	 */
	public function isSatisfiedBy(array $raw);
}
