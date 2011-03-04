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
namespace Appfuel\Framework\Init;

/**
 * Initialization strategy used to alter the servers php include_path
 */
class Includepath implements InitInterface
{
	/**
	 * @param	array	$params	
	 * @return	mixed
	 */
	public function init(array $params = array())
	{
		if (! array_key_exists('paths', $params)) {
			return FALSE;
		}

		$paths  = $params['paths'];
		$action = $params['action'];

		/* how to treat the existing include path */
		$validActions = array('append', 'prepend', 'replace');

		/* 
		 * when nothing is given we will override the current 
		 * includepath
		 */
		if (! in_array($action, $validActions)) {
			$action = 'replace';
		}

		/* a single path was passed in */
		if (is_string($paths) && ! empty($paths)) {
			$pathString = $paths);
		} else if (is_array($paths) && ! empty($paths)) {
			$pathString = implode(PATH_SEPARATOR, $paths);
		}

		$includePath = get_include_path();
		if ('append' === $action) {
			$pathString = $includePath PATH_SEPARATOR $pathString;
		} else if ('prepend' === $action) {
			$pathString .= PATH_SEPARATOR . $includePath;
		}
		
		return set_include_path($pathString);
	}
}
