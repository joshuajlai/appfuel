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
namespace Appfuel\Framework;

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
