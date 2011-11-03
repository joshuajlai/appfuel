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
namespace Appfuel\Kernal\Error;

/**
 * Normalize all possible values for representing on, off and standard error
 * to just 'on', 'off' and 'stderr'. Use those values to set the ini 
 * directive display_errors. Also provide functionality to display the current
 * status of ini_get('display_errors').
 */
class ErrorDisplay implements ErrorDisplayInterface
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
	 * Acceptable values to represent the state of off
	 * @var array
	 */
	protected $errorValues = array('stderr', 'err', 'error');

    /**
     * @return  bool
     */
    public function enable()
    {
        return $this->set('on');
    }

    /**
     * @return  bool
     */
    public function disable()
    {
        return $this->set('off');
    }

    /**
     * @return  bool
     */
    public function sendToStdError()
    {
        return $this->set('stderr');
    }

	/**
	 * Normalize all valid values into three standard options 
	 * 'on', 'off', 'stderr'
	 *
	 * @param	string	$value
	 * @return	mixed	string|false
	 */
	public function getValue($value)
	{
		$value = strtolower($value);
		if ($this->isValidOn($value)) {
			return 'on';
		} 
		else if ($this->isValidOff($value)) {
			return 'off';
		}
		else if($this->isValidError($value)) {
			return 'stderr';
		}

		return false;
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
	 * @param	mixed	$value
	 * @return	bool
	 */
	public function isValidError($value)
	{
		$checkType = true;
		if (in_array($value, $this->errorValues, $checkType)) {
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
    public function set($value)
    {
		$value = $this->getValue($value);
		if (false === $value) {
			return false;
		}

        $result = ini_set('display_errors', $value);
		if (false === $result) {
			return false;
		}

		return true;
    }

    /**
     * @return  string
     */
    public function get()
    {
        return ini_get('display_errors');
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

	/**
	 * @return array
	 */
	public function getValidErrorValues()
	{
		return $this->errorValues;
	}
}
