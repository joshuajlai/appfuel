<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *  
 * @category    Appfuel
 * @package     Util
 * @author      Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Appfuel\App;


/**
 * App Php Error
 
 */
class PhpError
{
	/**
	 * Levels
	 * Translation from constants to more easily readable name
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
	 * @return	PhpError
	 */
	public function __construct()
	{
        $this->levels['simple']     = E_ERROR | E_WARNING | E_PARSE;
		$this->levels['all_strict'] = E_ALL | E_STRICT;
	}

	/**
	 * @return	PhpError
	 */
	public function enableDisplay()
	{
		return $this->setDisplayStatus('on');
	}

	/**
	 * @return	PhpError
	 */
	public function disableDisplay()
	{
		return $this->setDisplayStatus('off');
	}

	/**
	 * consolidate many values into 1 for display and 0 for no display
	 * @return	
	 */
	public function setDisplayStatus($flag)
	{
		if (is_string($flag)) {
			$flag = strtolower($flag);
		}

		$result = NULL;
		switch($flag) {
			case 'on':
			case TRUE:
			case 1:
			case 'yes':
				$result = ini_set('display_errors', 1);
				break;
			case 'off':
			case FALSE:
			case 0:
			case 'no':
				$result =ini_set('display_errors', 0);
				break;
		}

		return $result;
	}

    /**
     * @return bool
     */
    public function getDisplayStatus()
    {
        $result =(int) ini_get('display_errors');
        return (1 === $result) ? TRUE : FALSE;
    }

	/**
	 * Maps a set of labels to th php constants for errors. The main reason
	 * for this is the use of contants in the ini file
	 */
	public function setReportingLevel($level, $raw = FALSE)
	{
		if (TRUE === $raw) {
			$key   = array_search($level, $this->levels,TRUE);
			if (FALSE === $key) {
				throw new Exception(
					"The error level ($level) has not been mapped and 
					therefore can not be set"
				);
			}
			error_reporting($level);
			return TRUE;
		}

		$level = $this->mapLevel($level);
		if (FALSE === $level) {
			throw new Exception(
				"The error level ($level) could not be mapped and therefore
				could not be set"
			);
		}
		error_reporting($level);
		return TRUE;
	}

	public function getReportingLevel($raw = FALSE)
	{
		$level = error_reporting();
		if (TRUE === $raw) {
			return $level;
		}
		
		return array_search($level, $this->levels, TRUE);	
	}

	public function mapLevel($error)
	{
		if (! isset($this->levels[$error])) {
			return FALSE;
		}

		return $this->levels[$error];
	}
}
