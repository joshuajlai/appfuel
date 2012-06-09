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
 * Filters email strings 
 */
class EmailFilter extends ValidationFilter
{
	/**
	 * @param	mixed $raw	input to filter
	 * @return	mixed 
	 */	
	public function filter($raw)
	{
		$options = array('options' => array());
		if ($this->isDefault()) {
			$options['options']['default'] = $this->getDefault();
		}
		
		$result = filter_var($raw, FILTER_VALIDATE_EMAIL, $options);

		if (! $result) {
			$result = $this->getFailure();
		}

		return $result;
	}
}
