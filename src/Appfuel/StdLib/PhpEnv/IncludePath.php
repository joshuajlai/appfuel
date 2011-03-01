<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *  
 * @category    Appfuel
 * @package     Util
 * @author      Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Appfuel\StdLib\PhpEnv;


/**
 * Customize the include spath to your own needs
 */
class IncludePath implements IncludePathInterface
{
	/**
	 * Include paths
	 * Directories paths to be used in set_include_path
	 * @var string
	 */
	protected $paths = '';

	/**
	 * Backup Paths
	 * Used for restore capabilities
	 * @var string
	 */
	protected $backupPaths = '';

	/**
	 * We can not assign with a bitwise mask in a member definition so
	 * make the assignment here
	 *
	 * @return	PhpError
	 */
	public function __construct(array $paths)
	{
		if (empty($paths)) {
			throw new Exception(
				"Can not create include path without a path"
			);
		}

		$this->paths = implode(PATH_SEPARATOR,$paths);
	}

	/**
	 * @return string
	 */
	public function getPaths()
	{
		return $this->paths;
	}

	/**
	 * @return string	the old include path
	 */
	public function enable()
	{
		$this->backup();
		$result = set_include_path($this->getPaths());
		if (FALSE === $result) {
			$this->restore();
			throw new Exception("Could not set include path");
		} 

		return $result;
	}

	/**
	 * @return string
	 */
	public function getBackup()
	{
		return $this->backupPaths;
	}

	/**
	 * @return NULL
	 */
	public function backup()
	{
		$this->backupPaths = get_include_path();
	}

	/**
	 * @return	FALSE | string 
	 */
	public function restore()
	{
		$paths = $this->getBackup();
		if (empty($paths)) {
			return FALSE;
		}

		return set_include_path($paths);
	}
}
