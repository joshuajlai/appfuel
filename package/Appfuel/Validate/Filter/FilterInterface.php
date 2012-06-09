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
 * Filter raw input into a known clean value
 */
interface FilterInterface
{
	/**
	 * Unique key used to indicate a filter failure
	 */
	const FAILURE = '__AF_FILTER_FAILURE__';

	/**
	 * Unique key used to indicate the default value has not been set
	 */
	const DEFAULT_NOT_SET = '__AF_DEFAULT_NOT_SET__';

    /**
     * @return mixed | special token string on failure
     */
	public function filter($raw);

	/**
	 * @param	FilterSpecInterface		$spec
	 * @return	FilterInterface
	 */
	public function loadSpec(FilterSpecInterface $spec);
}
