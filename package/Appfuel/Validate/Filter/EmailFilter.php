<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuele@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
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
