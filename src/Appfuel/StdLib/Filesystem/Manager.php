<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @category 	Appfuel
 * @package 	Filesystem
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    	http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace 	Appfuel\StdLib\Filesystem;

/**
 * Filesystem Manager
 * Provides a interface for filesystem operations
 * 
 * @package 	Appfuel
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
	 * @param 	string 	$className
	 * @return 	string
	 */
	static public function classNameToFilename($className)
	{
		return str_replace(
			array('\\','_'), 
			DIRECTORY_SEPARATOR, 
			$className
		) . '.php';
	}

	/**
	 * @param	File	$file	points to configuration file
	 * @param	bool	$sec	process sections TRUE gives you an array with
	 *							section names and settings 
	 * @param	int		$mode   controls behavior of parsing
	 */
	static public function parseIni(File    $file, 
									$sec  = FALSE, 
									$mode = INI_SCANNER_NORMAL)
	{
		if (! $file->isFile()) {
			throw new Exception("Failed operation: parseIni
				File given is not readable or does not exist"
			);
		}
	
		$sec  = (bool) $sec;
		$path = $file->getRealPath();
		return parse_ini_file($path, $section, $mode);
	}
}

