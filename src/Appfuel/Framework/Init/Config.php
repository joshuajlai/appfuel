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

use Appfuel\Stdlib\Filesystem\Manager as FileManager;

/**
 * Initialization strategy used to parse the ini file into and array
 * structure
 */
class Config implements InitInterface
{
	/**
	 * @param	array	$params	
	 * @return	mixed
	 */
	public function init(array $params = array())
	{
        
		if (! array_key_exists('env', $params)) {
            throw new Exception("Env not found and is required");
        }

        $section = $params['env'];
    
        if (! array_key_exists('file', $params)) {
            throw new Exception('file param not found and is required');
        }
        $file = FileManager::createFile($params['file']);
        $ini  = FileManager::parseIni($file);
        
        if (! is_array($ini) || ! array_key_exists($section, $ini)) {
            throw new Exception(
                "Config section could not be located with {$section} " .
                "or config file does not exist at {$params['file']}"
            );
        }

        $data = $ini[$section];
        if (array_key_exists('Prod', $ini)) {
            $data = array_merge($ini['Prod'], $data);
        }

		return $data;
	}
}
