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
namespace Appfuel\Stdlib\Filesystem;
 
use Appfuel\Framework\Exception;

/**
 * Aims to provide a common interface that can work on both file paths
 * as a string or as an SplFileInfo object 
 */
class Manager
{
	/**
	 * This will return the canonicalized absolute path of a file in the 
	 * current directory or in any directory listed in the include path.
	 * Also returns the same path given if that path is itself an absolute
	 * path that exists
	 * 
	 * @param 	string 	$file 	path to file
	 * @return	mixed 	FALSE|string
	 */
	static public function getAbsolutePath($file)
	{
		if (file_exists($file)) {
			return realpath($file);
		}

		$paths = explode(PATH_SEPARATOR, get_include_path());
		foreach ($paths as $path) {
			$filePath = $path . DIRECTORY_SEPARATOR . $file;
			if (file_exists($filePath)) {
				return realpath($filePath);
			}
		}
		return FALSE;
	}

	/**
	 * convert the class name to its relative path in the filesystem
	 *
	 * @param 	string 	$cname	class name
	 * @return 	string
	 */
	static public function classNameToFileName($cname)
	{
		if (empty($cname)) {
			return FALSE;
		}
		$file = str_replace(array('\\','_'), DIRECTORY_SEPARATOR, $cname);
		return trim($file, DIRECTORY_SEPARATOR) . '.php';
	}

	/**
	 * convert the class name to its relative path in the filesystem
	 *
	 * @param 	string 	$cname	class name
	 * @return 	string
	 */
	static public function classNameToDir($cname)
	{
		if (empty($cname)) {
			return FALSE;
		}

		$path = str_replace(array('\\','_'),DIRECTORY_SEPARATOR,$cname);
        return substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR));
	}

	/**
	 * @param	File	$file	points to configuration file
	 * @param	bool	$sec	process sections TRUE gives you an array with
	 *							section names and settings 
	 * @param	int		$mode   controls behavior of parsing
	 */
	static public function parseIni($file, 
									$sec = FALSE,
									$mode = INI_SCANNER_NORMAL)
	{
		$notFound = 'Faild operation:' .
					 'parseIni File given is not readable or does not exist';

		if (is_string($file)) {
			if (! file_exists($file)) {
				throw new Exception($notFound);
			}
			$path = $file;
		} elseif ($file instanceof \SplFileInfo) {
			if (! $file->isFile()) {
				throw new Exception($notFound);
			}
			$path = $file->getRealPath();
		} else {
			throw new Exception("Invalid Parameter file");
		}
		
	
		$sec  = (bool) $sec;
		return parse_ini_file($path, $sec, $mode);
	}
}
