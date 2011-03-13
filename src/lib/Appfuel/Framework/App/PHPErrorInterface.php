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
namespace Appfuel\Framework\App;

/**
 */
interface PHPErrorInterface
{
	/**
	 * Used to display the current reporting level in terms more readable
	 * then the bit masks and constants used in php. The raw flag is used
	 * to indicate php original values should be used
	 * 
	 * @param	bool	$raw
	 * @return	mixed
	 */
	public function getReportingLevel($raw = FALSE);

	/**
	 * Used to set the error reporting level via a set of codes that are
	 * intended to be more readable then the bit mask and constants used
	 * used in php
	 *
	 * @param	string	$code	the code to be mapped to error constant
	 * @param	bool	$raw	when TRUE don't map
	 * @reutrn	string
	 */ 
	public function setReportingLevel($code, $raw = FALSE);

	/**
	 * Used to set display_error setting with ini_set 
	 * 
	 * @param	string	$flag	'1' is on and '0' is off
	 * @return	string
	 */
	public function setDisplayStatus($flag);

	/**
	 * Display the current setting for display_errors
	 *
	 * @return string
	 */
	public function getDisplayStatus();
}
