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
namespace Appfuel\Kernel\Error;

/**
 * Uses a defined mapping between labels to error constants. Those labels
 * are used in configuration and resolved and applied here.
 */
class ErrorLevel implements ErrorLevelInterface
{
    /**
     * Translation from constants to more easily usable names
     * @var array
     */
    static protected $map =  array(
		'none'               => 0,
		'error'              => E_ERROR,
		'warning'            => E_WARNING,
		'parse'              => E_PARSE,
		'notice'             => E_NOTICE,
		'core_error'         => E_CORE_ERROR,
		'core_warning'       => E_CORE_WARNING,
		'compile_error'      => E_COMPILE_ERROR,
		'compile_warning'    => E_COMPILE_WARNING,
		'user_error'         => E_USER_ERROR,
		'user_warning'       => E_USER_WARNING,
		'user_notice'        => E_USER_NOTICE,
		'strict'             => E_STRICT,
		'recoverable_error'  => E_RECOVERABLE_ERROR,
		'deprecated'         => E_DEPRECATED,
		'user_deprecated'    => E_USER_DEPRECATED,
		'all'                => E_ALL,
	);

	/**
	 * @return	null
	 */
	public function enableAll()
	{
		error_reporting(-1);
	}

	/**
	 * @return null
	 */
	public function disableAll()
	{
		error_reporting(0);
	}

    /**
	 * Wraps the functionality of error_reporting. Excepts a comma separated
	 * string of codes that correspond to the php error constants. If -1 is
	 * is passed in then all codes will be used and if 0 is passed in then 
	 * all codes will be disabled. When a code in the comma separated list
	 * is prefixed with a dash then that code is treated as one to be disabled.
	 *
	 * @param	string	$codes	comma separated string to be included
	 * @return	null
     */
    public function setLevel($codes)
    {
		/*
		 * special treatment includes the following:
		 * empty string:	bail cause it makes no sense
		 * -1:				enable all
		 *  0:				disable all
		 */
		if (is_string($codes)  && empty($codes)) {
			return;
		}
		else if (-1 === $codes) {
			$this->enableAll();
			return;
		}
		else if (0 === $codes) {
			$this->disableAll();
			return;
		}

        $codes = explode(',', $codes, 15);
	
		/*
		 * separate out the codes into enabled and disabled. All disabled codes
		 * are prefixed with a '-' everthing else is considered enabled.
		 */
		$enabledCodes  = array();
		$disabledCodes = array();	
		foreach ($codes as $code) {
			$code = trim($code);
			if ('-' === $code[0]) {
				$code = substr($code, 1);
				/* trim to remove spaces between dash if they exist */
				$disabledCodes[] = trim($code);
			} else {
				$enabledCodes[] = $code;
			}
			
		}

		/* both sets of codes can not be empty */		
		if (empty($enabledCodes) && empty($disabledCodes)) {
			return;
		}

		/*
		 * perform bitwise OR operations to collect all coded into a single
		 * integer value for both enabled and disabled coded
		 */
		$disabled = null;
		$enabled  = $this->getBitValue($enabledCodes);
		if (! empty($disabledCodes)) {
			$disabled = $this->getBitValue($disabledCodes);
		}

		/*
		 * The truth table for enabled disabled states is as follows:
		 * enable disabled
		 *  null	null	invalid state return
		 *  null	int		replace enable with current state negate disabled
		 *  int		null	use enabled as the level ignore disabled
		 *  int     int     use enabled and negate it with disabled
		 */
		if (null === $enabled && null === $disabled) {
			return;
		}
		else if (! is_int($enabled) && is_int($disabled)) {
			$level = error_reporting() & ~ $disabled;
		}
		else if (is_int($enabled) && ! is_int($disabled)) {
			$level = $enabled;
		}
		else if (is_int($enabled) && is_int($disabled)) {
			$level = $enabled & ~$disabled;
		} 

		error_reporting($level);
    }

    /**
     * Map the current reporting level into an array of enabled and disabled
	 * levels
	 *
     * @return  array
     */
    public function getLevel()
    {
        $level    = error_reporting();
		$bitCount = 15;
		$result   = array(
			'disabled' => array(),
			'enabled'  => array()
		);

		/*
		 * 15 bits are used to to store all the bit levels. In order to 
		 * determine which levels are enabled and disabled we have to examine
		 * each bit and determine if its enabled or disabled
		 */
		for ($i=0; $i < $bitCount; $i++) {
			
			/* shift to the correct bit for testing */
			$bitValue = $level & (1 << $i);
			if (0 === $bitValue) {
				$bitValue = pow(2, $i);
				$result['disabled'][] = $this->mapLevel($bitValue);
			} else {
				$result['enabled'][] = $this->mapLevel($bitValue);
			}
		}
        return $result;
    }

    /**
     * @param   int $errorLevel     
     * @return  FALSE|string
     */
    public function mapLevel($errorLevel)
    {
        return array_search($errorLevel, self::$map, TRUE);
    }

    /**
     * Returns the PHP Constant for the given code if mapped
     *
	 * @param   string  $code 
     * @return  FALSE|int
     */
    public function mapCode($code)
    {
        if (! $this->isCode($code)) {
            return FALSE;
        }

        return self::$map[$code];
    }

    /**
     * Determins if a given error code exists in the map
     *
	 * @param   string  $code
     * @return  bool
     */
    public function isCode($code)
    {
        if (! array_key_exists($code, self::$map)) {
            return FALSE;
        }

        return TRUE;
    }

	/**
	 * @return array
	 */
	public function getMap()
	{
		return self::$map;
	}

	/**
	 * Bitwise OR all codes that can be mapped into there error levels
	 *
	 * @param	array	$codes
	 * @return	mixed	int|null
	 */
	protected function getBitValue(array $codes)
	{
		$bitValue = NULL;
		foreach ($codes as $code) {
			$level = $this->mapCode($code);
			if (false === $level) {
				continue;
			}
			$bitValue |= $level;
		}

		return $bitValue;
	}
}
