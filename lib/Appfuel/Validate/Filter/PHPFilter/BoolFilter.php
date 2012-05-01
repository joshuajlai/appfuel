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

use Appfuel\Validate\Filter\ValidateFilter,
	Appfuel\DataStructure\DictionaryInterface;

/**
 * Filters bool values: 
 * true is considered "1", "on" and "yes", true, 1
 * false is considered anything not true unless strict is given then
 * false is "0", "off", "no" and "" and failure token is returned for all
 * other values not true or false.
 */
class BoolFilter extends ValidateFilter
{
	/**
	 * @param	mixed				$raw	input to filter
	 * @param	DictionaryInteface	$params		used to control filtering
	 * @return	mixed | failedFilterToken 
	 */	
	public function filter($raw, DictionaryInterface $params)
	{
		$this->clearFailure();

		$default = $params->get('default', null);
		$options = array('options' => array());
		if (null !== $default) {
			$options['options']['default'] = $default;
		}
		
		if ($params->get('strict', false)) {
			$options['flags'] = FILTER_NULL_ON_FAILURE;
		}

		$result = filter_var($raw, FILTER_VALIDATE_BOOLEAN, $options);

		if (null === $result) {
			$this->enableFailure();
			return null;
		}

		return $result;
	}
}
