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

/**
 * Value object used to determine how a field is validated/filtered 
 */
interface FieldSpecInterface
{
	/**
	 * @return string
	 */
	public function getFields();

	/**
	 * @return string
	 */
	public function getLocation();

	/**
	 * @return	string
	 */
	public function getFilterSpec();

	/**
	 * @return	string
	 */
	public function getFilters();

	/**
	 * @return	string
	 */
	public function getValidator();
}
