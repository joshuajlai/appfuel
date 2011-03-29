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
namespace Appfuel\Framework\Init;

use Appfuel\Stdlib\Filesystem\Manager as FileManager;

/**
 * Automatic look for the class file, the location of which is encoded
 * in the class name. The forward slash is converted to a directory 
 * separator
 */
class Autoloader implements AutoloadInterface
{
    /**
     * Wrapper for the spl_autoload_register. 
     * @return  bool
     */
    public function register()
    {
		$functions = spl_autoload_functions();

		/* 
		 * check to see if the method is already registered
		 */
		if (is_array($functions)) {
			foreach ($functions as $data) {
				
				/*
				 * a string would indicate just a function not a class 
				 * with a method, so this could never be our autoloader
				 */
				if (is_string($data)) {
					continue;
				} else if (is_array($data) && 2 === count($data)) {
					if ($data[0] instanceof Autoloader && 
								'loadClass' === $data[1]) {
						return FALSE;
					} 
				}
			}
		}

        return spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Wrapper for the spl_autoload_unregister
     * @return  bool
     */
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * @return bool
     */
    public function isLoaded($className)
    {
        return class_exists($className, FALSE) || 
			   interface_exists($className, FALSE);
    }

    /**
     * Will be registered to handle autoload requested class names
     * @param   string  $className  
     * @return  NULL
     */
    public function loadClass($className)
    {
        if ($this->isLoaded($className)) {
            return;
        }

        $fileName = FileManager::classNameToFileName($className);
        $filePath = FileManager::getAbsolutePath($fileName);
        if (FALSE === $filePath) {
            throw new \Exception(
                "Autoload Error: could not find class: $className for file
                $fileName"
            );
        }
        require_once $filePath;
    }
}
