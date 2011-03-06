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

		/* a single path was passed in */
		if (is_string($paths) && ! empty($paths)) {
			$pathString = $paths;
		} else if (is_array($paths) && ! empty($paths)) {
			$pathString = implode(PATH_SEPARATOR, $paths);
		}
	
		/*
		 * The default action is to replace the include path. If
		 * action is given with either append or prepend the 
		 * paths will be concatenated accordingly
		 */
		$includePath = get_include_path();
		if ('append' === $action) {
			$pathString = $includePath . PATH_SEPARATOR . $pathString;
		} else if ('prepend' === $action) {
			$pathString .= PATH_SEPARATOR . $includePath;
		}
		
		return set_include_path($pathString);
	}
}
