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
 * Filters email strings 
 */
class EmailFilter extends ValidateFilter
{
	/**
	 * @param	mixed				$raw	input to filter
	 * @param	DictionaryInteface	$params		used to control filtering
	 * @return	mixed | failedFilterToken 
	 */	
	public function filter($raw, DictionaryInterface $params)
	{
		$default = $params->get('default', null);
		$options = array('options' => array());
		if (null !== $default) {
			$options['options']['default'] = $default;
		}
		
		$result = filter_var($raw, FILTER_VALIDATE_EMAIL, $options);

		if (! $result) {
			return $this->failedFilterToken();
		}

		return $result;
	}
}
