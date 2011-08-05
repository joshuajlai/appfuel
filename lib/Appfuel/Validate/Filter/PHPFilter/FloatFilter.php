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
		
		$result = filter_var($raw, FILTER_VALIDATE_FLOAT, $options);
		if (false === $result) {
			return $this->failedFilterToken();
		}

		return $result;
	}
}
