<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Validate\Filter;

use Appfuel\DataStructure\DictionaryInterface;

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
		$fail  = $this->getFailureToken();
		$clean = $this->filterVar($raw, $fail);
		if ($fail === $clean) {
			return $fail;
		} 

		/*
		 * implments options like in or not-in
		 */
		$clean = $this->filterSet($clean, $fail);
		if ($fail === $clean) {
			return $fail;
		}

		return $clean;
	}


	/**
	 * Use php filter_var for basic validation
	 * 
	 * @param	string	
	 * return	mixed
	 */
	public function filterVar($raw, $failToken)
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
		
		$clean = filter_var($raw, FILTER_VALIDATE_INT, $options);
		if (false === $clean) {
			return $failToken;
		}

		return $clean;
	}

	public function filterSet($raw, $failToken)
	{
		$set = $this->getOption('in');
		if (is_array($set)) {
			if (in_array($raw, $set, true)) {
				return $raw;
			}
			return $failToken;
		}

		$set = $this->getOption('not-in');
		if (is_array($set)) {
			if (! in_array($raw, $set, true)) {
				return $raw;
			}
			return $failToken;
		}

		return $raw;
	}
}
