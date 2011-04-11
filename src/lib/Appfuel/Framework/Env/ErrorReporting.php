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
 * Map php error codes to more readable interface
 */
class ErrorReporting
{
    /**
     * Translation from constants to more easily usable names
     * @var array
     */
    protected $map =  array(
		'none'               => 0,
		'error'              => E_ERROR,
		'warning'            => E_WARNING,
		'parse'              => E_PARSE,
		'notice'             => E_NOTICE,
		'core_error'         => E_CORE_ERROR,
		'core_warning'       => E_CORE_WARNING,
		'complile_error'     => E_COMPILE_ERROR,
		'complile_warning'   => E_COMPILE_WARNING,
		'user_error'         => E_USER_ERROR,
		'user_warning'       => E_USER_WARNING,
		'user_notice'        => E_USER_NOTICE,
		'strict'             => E_STRICT,
		'recoverable_error'  => E_RECOVERABLE_ERROR,
		'deprecated'         => E_DEPRECATED,
		'user_deprecated'    => E_USER_DEPRECATED,
		'all'                => E_ALL,
		'future_all'		 => -1
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
	 *
	 * @param	string	$enabledCodes	comma separated string to be included
	 * @param	string	$disabledCodes	comman separated codes to be excluded
	 * @return	null
     */
    public function setLevel($enabledCodes = 'all', $disabledCodes = '')
    {
		$enabled  = $this->getBitValue($enabledCodes);
		$disabled = NULL;
		if (! empty($disabledCodes)) {
			$disabled = $this->getBitValue($disabledCodes);
		}
	
		if (NULL !== $disabled) {
			$level = $enabled & ~$disabled;
		} else {
			$level = $enabled;
		}


		error_reporting($level);
    }

    /**
     * Map the current reporting level into an array of enabled and disabled
	 * levels
	 *
	 * @param   bool    $raw    ignore our mapping
     * @return  string
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
        return array_search($errorLevel, $this->map, TRUE);
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

        return $this->map[$code];
    }

    /**
     * Determins if a given error code exists in the map
     *
	 * @param   string  $code
     * @return  bool
     */
    public function isCode($code)
    {
        if (! array_key_exists($code, $this->map)) {
            return FALSE;
        }

        return TRUE;
    }

	/**
	 * @return array
	 */
	public function getMap()
	{
		return $this->map;
	}

	protected function getBitValue($codes)
	{
        $codes    = explode(',', $codes, 15);
		$bitValue = NULL;
		foreach ($codes as $code) {
			$level = $this->mapCode(trim($code));
			if (false === $level) {
				continue;
			}
			$bitValue |= $level;
		}

		return $bitValue;
	}
}
