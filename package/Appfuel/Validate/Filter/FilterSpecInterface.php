<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Validate\Filter;

/**
 * Value object used to hold information about a filter
 */
interface FilterSpecInterface
{
	/**
	 * @return	string
	 */
	public function getName();

	/**
	 * @return	array
	 */
	public function getOptions();

	/**
	 * @return	string
	 */
	public function getError();
}
