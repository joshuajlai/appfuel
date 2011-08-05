<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Validate\Filter\PHPFilter;

use Appfuel\Framework\Exception,
	Appfuel\Validate\Filter\ValidateFilter,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * Validate floating point numbers with native php filter_var function
 */
class FloatFilter extends ValidateFilter
{
	/**
	 * @return	string
	 */	
	public function filter($raw, DictionaryInterface $params)
	{
		$default = $params->get('default', null);
		$options = array('options' => array());
		if (null !== $default) {
			$options['options']['default'] = $default;
		}

		$decimal = $params->get('decimal-sep', false);
		if (is_string($decimal) && ! empty($decimal)) {
			$options['options']['decimal'] = $decimal;
		}

		/*
		 * allow the use of thousand marker
		 */
		if ($params->get('allow-thousands', false)) {
			$options['flags'] = FILTER_FLAG_ALLOW_THOUSAND;
		}

		/* will bitwise or the flag if it exists */
		if ($params->get('allow-fractions', false)) {
			if (! isset($options['flag'])) {
				$options['flags'] = FILTER_FLAG_ALLOW_FRACTION;
			}
			else {
				$options['flag'] |= FILTER_FLAG_ALLOW_FRACTION;
			}
		}
		
		$result = filter_var($raw, FILTER_VALIDATE_FLOAT, $options);
		if (false === $result) {
			return $this->failedFilterToken();
		}

		return $result;
	}
}
