<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *  
 * @category    Appfuel
 * @package     Spark
 * @author      Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Appfuel\Spark;

/**
 * Configure Interface
 */
interface ConfigureInterface
{
	public function configure($option, $method, array $params = array());
}
