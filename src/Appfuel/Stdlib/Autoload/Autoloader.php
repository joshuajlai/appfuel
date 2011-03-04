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
namespace Appfuel\Stdlib\Autoload;

use Appfuel\Stdlib\Filesystem\Manager as FileManager;

/**
 * Automatic look for the class file, the location of which is encoded
 * in the class name. The forward slash is converted to a directory 
 * separator
 */
class Autoloader
{
    /**
     * list of registed autoloaders on the stack
     * @var array
     */
    protected $backup = NULL;

    /**
     * @return mixed FALSE|array
     */
    public function getRegistered()
    {
        return spl_autoload_functions();
    }

    /**
     * @return void
     */
    public function backupRegistered()
    {
        $this->backup = $this->getRegistered();
    }

    /**
     * Get a list of saved autoloader. usually to restore
     * @return array
     */
    public function getBackup()
    {
        return $this->backup;
    }

    /**
     * Returns an array of autoloaders that was just cleared
     * @return array
     */
    public function clearAutoloaders($backup = TRUE)
    {
        if (TRUE === $backup) {
            $this->backupRegistered();
        }

        $loaders = $this->getRegistered();
        foreach ($loaders as $loader) {
            
			/* 
			 * The registered functions are returned as a string
			 * or an array with with class name as the first item
			 * and the method as the second
			 */
			if (is_string($loader)) {
                spl_autoload_unregister($loader);
            } else if (is_array($loader) && 2 == count($loader)) {
                spl_autoload_unregister(array($loader[0], $loader[1]));
            }
        }

        return $loaders;
    }

    /**
     * @return FALSE|array
     */
    public function restoreAutoloaders()
    {
        $loaders = $this->getBackup();
        if (NULL === $loaders) {
            return FALSE;
        }

		/* 
		 * The registered functions are returned as a string
		 * or an array with with class name as the first item
		 * and the method as the second
		 */
        foreach ($loaders as $loader) {
            if (is_string($loader)) {
                spl_autoload_register($loader);
            } else if (is_array($loader) && 2 == count($loader)) {
                spl_autoload_register(array($loader[0], $loader[1]));
            }
        }

        return $loaders;
    }

    /**
     * Wrapper for the spl_autoload_register. 
     * @return  bool
     */
    public function register()
    {
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
        return class_exists($className, FALSE) || interface_exists($className);
    }

    /**
     * Will be registered to handle autoload requested class names
     * @param   string  $className  
     * @return  void
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

