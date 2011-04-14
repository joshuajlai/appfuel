<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Framework\Env;

/**
 * Generalize the logic necessary for setting and getting most ini 
 * configuration directives.
 */
class PhpIniDirective
{
	/**
	 * Acceptable values to represent the state of on
	 * @var array
	 */
	protected $onValues = array('on', 'yes', '1', 1);

	/**
	 * Acceptable values to represent the state of off
	 * @var array
	 */
	protected $offValues = array('off', 'no', '0', 0);

    /**
     * @return  bool
     */
    public function enableSetting($field)
    {
        return $this->setIni($field, 'on');
    }

    /**
     * @return  bool
     */
    public function disableSetting($field)
    {
        return $this->setIni($field, 'off');
    }

	/**
	 * @param	mixed	$value
	 * @return	bool
	 */
	public function isValidOn($value)
	{
		$checkType = true;
		if (in_array($value, $this->onValues, $checkType)) {
			return true;
		}

		return false;
	}

	/**
	 * @param	mixed	$value
	 * @return	bool
	 */
	public function isValidOff($value)
	{
		$checkType = true;
		if (in_array($value, $this->offValues, $checkType)) {
			return true;
		}

		return false;
	}

    /**
	 * Normailze the values so they are always 'on', 'off' or 'stderr'
	 * then use that value to set the ini directive
	 *
	 * @param	string	$value
	 * @return  bool
     */
    public function setIni($field, $value)
    {
		if (! $this->validString($field)) {
			return false;
		}

        $result = ini_set($field, $value);
		if (false === $result) {
			return false;
		}

		return true;
    }

    /**
     * @return  string
     */
    public function getIni($field)
    {
		if (! $this->validString($field)) {
			return false;
		}

        return ini_get($field);
    }

	/**
	 * @return array
	 */
	public function getValidOnValues()
	{
		return $this->onValues;
	}

	/**
	 * @return array
	 */
	public function getValidOffValues()
	{
		return $this->offValues;
	}

	protected function isValidString($value)
	{
		if (is_string($value) && ! empty($value)) {
			return true;
		}

		return false;
	}
}
