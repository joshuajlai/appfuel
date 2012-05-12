<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\ClassLoader;

use LogicException,
	DomainException;

/**
 * Load each item in an associative array of class-name => path into memory.
 */
class ManualClassLoader
{
	/**
	 * @param	string	$name	class name to load
	 * @param	string	$path	absolute path to php file
	 * @return	bool
	 */
	static public function loadClass($name, $file, $isPkgPath = true)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'class name must be a none empty string';
			throw new DomainException($err);
		}

        if (class_exists($name, false) || interface_exists($name, false)) {
			return true;    
		}

        $pkgPath = '';
        if (is_array($file)) {
            $filePath = current($file);
            $pkgPath  = next($file);
            if (! is_string($filePath) || ! is_string($pkgPath)) {
                $err = "path to php code and file path must be strings";
                throw new DomainException($err);
            }
            
            $file = AF_BASE_PATH . DIRECTORY_SEPARATOR;
            if (! empty($pkgPath)) {
                $file .= $pkgPath     . DIRECTORY_SEPARATOR;
            }
            $file .= $filePath;
            $isPkgPath = false;
        }
        else if (! is_string($file)) {
			$err = 'file path must be a string with no -(../) chars ';
			throw new DomainException($err);
		}

		if (true === $isPkgPath) {
			if (! defined('AF_CODE_PATH')) {
				$err = 'lib path is needed but not defined';
				throw new LogicException($err);
			}
			
			$file = AF_CODE_PATH . DIRECTORY_SEPARATOR . $file;
		}

		if (! file_exists($file)) {
			$err = "could not manually load file at -($file)";
			throw new DomainException($file);
		}

		require $file;
		return true;
	}

	/**
	 * @throws	LogicException
	 * @throws	DomainException
	 * @param	string	$path	
	 * @param	bool	$isLibPath
	 * @return	true
	 */
	static public function loadCollectionFromFile($path, $isPkgPath = true)
	{
        if (! file_exists($path)) {
            $err = "could not find dependency file at -($path)";
            throw new DomainException($err);
        }

		$list = require $path;
		if (! is_array($list)) {
			$err  = "file -($file) with collection of dependencies needs ";
			$err .= "to return an array";
			throw new DomainException($err);
		}

		return self::loadCollection($list, $isPkgPath);
	}

	/**
	 * @throws	LogicException
	 * @param	array	$list	
	 * @param	bool	$isLibPath
	 * @return	true
	 */
	static public function loadCollection(array $list, $isPkgPath = true)
	{
		foreach ($list as $name => $path) {
			self::loadClass($name, $path, $isPkgPath);
		}

		return true;
	}
}
