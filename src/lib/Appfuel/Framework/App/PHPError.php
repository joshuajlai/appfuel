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
namespace Appfuel\Framework\App;

/**
 * Map php error codes to more readable interface
 */
class PHPError implements PHPErrorInterface
{
    /**
     * Translation from constants to more easily usable names
     * @var array
     */
    protected $levels = array(
        'none'              => 0,
        'error'             => E_ERROR,
        'warning'           => E_WARNING,
        'parse'             => E_PARSE,
        'notice'            => E_NOTICE,
        'strict'            => E_STRICT,
        'coreError'         => E_CORE_ERROR,
        'coreWarning'       => E_CORE_WARNING,
        'complileError'     => E_COMPILE_ERROR,
        'complileWarning'   => E_COMPILE_WARNING,
        'userError'         => E_USER_ERROR,
        'userWarning'       => E_USER_WARNING,
        'userNotice'        => E_USER_NOTICE,
        'userdeprecated'    => E_USER_DEPRECATED,
        'recoverableError'  => E_RECOVERABLE_ERROR,
        'deprecated'        => E_DEPRECATED,
        'all'               => E_ALL
    );

    /**
     * We can not assign with a bitwise mask in a member definition so
     * make the assignment here
     *
     * @return  PhpError
     */
    public function __construct()
    {
        $this->levels['standard']   = E_ERROR | E_WARNING | E_PARSE;
        $this->levels['all_strict'] = E_ALL | E_STRICT;
    }

    /**
     * @return  string  returns the previous display status
     */
    public function enableDisplay()
    {
        return $this->setDisplayStatus('1');
    }

    /**
     * @return  string  returns the previous display status
     */
    public function disableDisplay()
    {
        return $this->setDisplayStatus('0');
    }

    /**
     * @return  string  returns the previous display status
     */
    public function sendToStdErr()
    {
        return $this->setDisplayStatus('stderr');
    }

    /**
     * consolidate many values into 1 for display and 0 for no display
     *
	 * @param	string	$flag
	 * @return  string
     */
    public function setDisplayStatus($flag)
    {
		$flag = (string) $flag;
		$valid = array('1', '0', 'stderr');
		
		if (! in_array($flag, $valid)) {
			$flag = '0';
		}

        return ini_set('display_errors', $flag);
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
        if (TRUE === $raw) {
            return error_reporting($code);
        }

        $level = $this->getLevel($code);
        if (FALSE === $level) {
			$level = $this->getLevel('none');
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
}
