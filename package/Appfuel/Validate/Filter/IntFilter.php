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
 * Create the filter from the name given. In this case the 
 */
class IntFilter extends ValidationFilter
{
	/**
	 * @return	string
	 */	
	public function filter($raw)
	{
		$clean = $this->filterVar($raw);
		if ($this->isFailure($clean)) {
			return $this->getFailure();
		} 

		/*
		 * implments options like in or not-in
		 */
		$clean = $this->filterSet($clean);
		if ($this->isFailure($clean)) {
			return $this->getFailure();
		}

		return $clean;
	}

	/**
	 * Use php filter_var for basic validation
	 * 
	 * @param	string	
	 * return	mixed
	 */
	public function filterVar($raw)
	{
		$opts = array();
		if (null !== ($default = $this->getOption('default'))) {
			$opts['options']['default'] = $default;
		}
		
		if (null !== ($min = $this->getOption('min'))) {
			$opts['options']['min_range'] = $min;
		}

		if (null !== ($max = $this->getOption('max'))) {
			$opts['options']['max_range'] = $max;
		}

		if (true === $this->getOption('allow-octal', false)) {
			$opts['flags'] = FILTER_FLAG_ALLOW_OCTAL;
		}
		else if (true === $this->getOption('allow-hex', false)) {
			$options['flags'] = FILTER_FLAG_ALLOW_HEX;
		}
		
		$clean = filter_var($raw, FILTER_VALIDATE_INT, $opts);
		
		return (false === $clean) ? $this->getFailureToken() : $clean;
	}
	
	/**
	 * @param	mixed	$raw
	 * @return	mixed
	 */
	public function filterSet($raw)
	{
		$set = $this->getOption('in');
		if (is_array($set)) {
			if (in_array($raw, $set, true)) {
				return $raw;
			}

			return $this->getFailureToken();
		}

		$set = $this->getOption('not-in');
		if (is_array($set)) {
			if (! in_array($raw, $set, true)) {
				return $raw;
			}

			return $this->getFailureToken();
		}

		return $raw;
	}
}
