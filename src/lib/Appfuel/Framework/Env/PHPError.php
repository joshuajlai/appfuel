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
class PHPError implements PHPErrorInterface
{
    /**
     * Translation from constants to more easily usable names
     * @var array
     */
    protected $levels =  array(
		'none'              => 0,
		'error'             => E_ERROR,
		'warning'           => E_WARNING,
		'parse'             => E_PARSE,
		'notice'            => E_NOTICE,
		'coreError'         => E_CORE_ERROR,
		'coreWarning'       => E_CORE_WARNING,
		'complileError'     => E_COMPILE_ERROR,
		'complileWarning'   => E_COMPILE_WARNING,
		'userError'         => E_USER_ERROR,
		'userWarning'       => E_USER_WARNING,
		'userNotice'        => E_USER_NOTICE,
		'strict'            => E_STRICT,
		'recoverableError'  => E_RECOVERABLE_ERROR,
		'deprecated'        => E_DEPRECATED,
		'userdeprecated'    => E_USER_DEPRECATED,
		'all'               => E_ALL
	);

	/**
	 * White list of acceptable values for display_errors directive
	 * 
	 * @var array
	 */
	protected $displayWhiteList = array(
		'on',
		'off', 
		'yes',
		'no',
		'1',
		'0',
		1,
		0,
		'stderr',
	);

    /**
     * @return  string  returns the previous display status
     */
    public function enableErrorDisplay()
    {
        return $this->setDisplayStatus('on');
    }

    /**
     * @return  string  returns the previous display status
     */
    public function disableErrorDisplay()
    {
        return $this->setDisplayStatus('off');
    }

    /**
     * @return  string  returns the previous display status
     */
    public function sendToStdErr()
    {
        return $this->setDisplayStatus('stderr');
    }

	/**
	 * @param	string	$value
	 * @return	bool
	 */
	public function isValidDisplayValue($value)
	{
		$value = strtolower($value);
		return in_array($value, $this->displayWhiteList, true);
	}

    /**
     * check the value against a white list of on and off values and
	 * sets the status to the result of that check. If the value is not
	 * mapped or the php call fails false is returned
     *
	 * @param	string	$value
	 * @return  bool
     */
    public function setDisplayStatus($value)
    {
		if (! $this->isValidDisplayValue($value)) {
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
    public function getDisplayStatus()
    {
        return ini_get('display_errors');
    }

    /**
     * Maps a set of labels to th php constants for errors. The main reason
     * for this is the use of contants in the ini file
	 *
	 * @param	string	$code
	 * @param	bool	$raw
     */
    public function setReportingLevel($code, $raw = FALSE)
    {
        if (true === $raw) {
            return error_reporting($code);
        }

        $level = $this->getLevel($code);
        if (FALSE === $level) {
			return false;
        }

        return error_reporting($level);
    }

    /**
     * Map the current reporting level to our readable names
     *
	 * @param   bool    $raw    ignore our mapping
     * @return  string
     */
    public function getReportingLevel($raw = FALSE)
    {
        $level = error_reporting();
        if (TRUE === $raw) {
            return $level;
        }

        return $this->getCode($level);
    }

    /**
     * @param   int $errorLevel     
     * @return  FALSE|string
     */
    public function getCode($errorLevel)
    {
        return array_search($errorLevel, $this->levels, TRUE);
    }

    /**
     * Returns the PHP Constant for the given code if mapped
     *
	 * @param   string  $code 
     * @return  FALSE|int
     */
    public function getLevel($code)
    {
        if (! $this->isCode($code)) {
            return FALSE;
        }

        return $this->levels[$code];
    }

    /**
     * Determins if a given error code exists in the map
     *
	 * @param   string  $code
     * @return  bool
     */
    public function isCode($code)
    {
        if (! array_key_exists($code, $this->levels)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * @return array
     */
    public function getCodes()
    {
        return $this->levels;
    }

	protected function checkBit($value, $bitNumber, $size = '32')
	{
		$value =(int) $value;
		if ($value & (1 << ($size - $bitNumber))) {
			return true;
		}

		return false;
	}
}
