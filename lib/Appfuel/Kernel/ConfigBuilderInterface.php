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
namespace Appfuel\Kernel;

/**
 * Build a config file from merging two enviroment specific config files 
 * togather
 */
interface ConfigBuilderInterface
{
	/**
	 * @return	string
	 */
	public function getMergeEnv();

	/**
	 * @return string
	 */
	public function getCurrentEnv();

	/**
	 * @param	scalar	$char
	 * @return	ConfigBuilder
	 */
	public function setCurrentEnv($env);

}
