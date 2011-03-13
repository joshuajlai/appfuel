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
namespace Appfuel\Framework\App;

/**
 * Describes the necessary method's need for an autoloader to work with 
 * Appfuel
 */
interface AutoloadInterface
{
	/**
	 * Used to register the autoloader
	 * @return	bool
	 */
	public function register();
	public function unregister();
}
