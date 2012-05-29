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

/**
 * Filters bool values: 
 * true is considered "1", "on" and "yes", true, 1
 * false is considered anything not true unless strict is given then
 * false is "0", "off", "no" and "" and failure token is returned for all
 * other values not true or false.
 */
class BoolFilter extends ValidationFilter
{
	/**
	 * @param	mixed	$raw	input to filter
	 * @return	mixed
	 */	
	public function filter($raw)
	{

		$map = $this->getOption('map');
		if (is_array($map) && ! empty($map)) {
			$clean = $this->filterMap($raw, $map);
		}
		else {
			$clean = $this->filterVar($raw);
		}

		if ($this->isFailure($clean)) {
			return $this->getFailure();
		}

		return $clean;
	}

	/**
	 * uses php's filter_var to clean boolean values
	 *
	 * @param	mixed	$raw
	 * @return	bool | string
	 */
	protected function filterVar($raw)
	{
		if (is_bool($raw)) {
			return $raw;
		}

		$opts = array('options' => array());
		if ($this->isDefault()) {
			$opts['options']['default'] = $this->getDefault();
		}
		
		if (true === $this->getOption('strict', false)) {
			$opts['flags'] = FILTER_NULL_ON_FAILURE;
		}

		$clean = filter_var($raw, FILTER_VALIDATE_BOOLEAN, $opts);
		return (null === $clean) ? $this->getFailureToken() : $clean;
	}

	/**
	 * Allows for a custom map of true and false values
     *
	 * @param	mixed	$raw	
	 * @param	array	$map
	 * @return	mixed
	 */
	protected function filterMap($raw, array $map)
	{
		if (! isset($map['true']) || ! is_array($map['true'])) {
			return $this->getFailureToken();
		}
		$truthy = $map['true'];

		if (in_array($raw, $truthy, true)) {
			return true;
		}
		
		$isStrict = $this->getOption('strict', false);
		if (! $isStrict) {
			return false;
		}

		if (! isset($map['false']) || ! is_array($map['false'])) {
			return $this->getFailureToken();
		}
		$falsey = $map['false'];
		
		if (in_array($raw, $falsey, true)) {
			return false;
		}

		return $this->getFailureToken();
	}
}
