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
namespace Appfuel\Kernel;


/**
 * This interface hides the knowledge of the base path or base path with 
 * some relative path and allows the developer to generate a absolute path
 * using getPath. This means the application only needs to know the relative
 * location of any of the appfuel files.
 */
interface PathFinderInterface
{
	/**
	 * This is the AF_BASE_PATH
	 * @return string
	 */
	public function getBasePath();

	/**
	 * Disable the use of the base path
	 * @return	PathFinderInterface
	 */
	public function disableBasePath();
	
	/**
	 * @return	PathFinderInterface
	 */
	public function enableBasePath();
	
	/**
	 * @return	bool
	 */
	public function isBasePathEnabled();
	
	/**
	 * @return	string
	 */
	public function getRelativeRootPath();
	
	/**
	 * @param	string	$path
	 * @return	PathFinderInterface
	 */
	public function setRelativeRootPath($path);

	/**
	 * @return	string
	 */
	public function resolveRootPath();
	
	/**
	 * @param	string $path
	 * @return	string
	 */
	public function getPath($path = null);
}
