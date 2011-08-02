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
namespace Appfuel\Validate\Filter;

use Appfuel\Framework\Exception,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * Create the filter from the name given. In this case the 
 */
class IntFilter extends ValidateFilter
{
	/**
	 * @return	string
	 */	
	public function filter($raw, DictionaryInterface $params)
	{
		$default = $param->get('default', null);
		$options = array('options' => array());
		if (null !== $default) {
			$options['options']['default'] = $default;
		}
		
		$min = $params->get('min', null);
		if (null !== $min) {
			$options['options']['min_range'] = $min;
		}

		$max = $params->get('max', null);
		if (null !== $min) {
			$options['options']['max_range'] = $min;
		}

		if ($params->get('allow-octal', false)) {
			$options['flags'] = FILTER_FLAG_ALLOW_OCTAL;
		}
		else if ($params->get('allow-hex', false)) {
			$options['flags'] = FILTER_FLAG_ALLOW_HEX;
		}

		$result = filter_var($raw, FILTER_VALIDATE_INT, $options);
		if (false === $result) {
			return $this->failedFilterToken();
		}

		return $raw;
	}
}
