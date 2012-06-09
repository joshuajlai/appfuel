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
 * All validators must extend from this interface
 */
interface ValidatorInterface
{
	/**
	 * @param	CoordinatorInterface $coord
	 * @return	bool
	 */
	public function isValid(CoordinatorInterface $coord);
}
