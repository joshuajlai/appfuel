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

use Appfuel\Framework\StartupFactoryInterface;
use Appfuel\Framework\AutoloadInterface;

/**
 * Initialization strategy used to register the autoloader
 */
class Autoload implements InitInterface
{
	/**
	 * @param	array	$params	
	 * @return	bool
	 */
	public function init(array $params = array())
	{		
		if (! array_key_exists('startFactory', $params)) {
			return FALSE;
		}
		
		$factory = $params['startupFactory'];
		if (! $factory instanceof StartupFactoryInterface) {
			return FALSE;
		}

		$autoloader = $factory->createStartupFactory();
		if (! $autoloader instanceof AutoloadInterface) {
			return FALSE;
		}

		$autoloader->register();
		return TRUE;
	}
}
