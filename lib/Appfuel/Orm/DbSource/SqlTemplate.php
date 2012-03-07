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
namespace Appfuel\Orm\DbSource;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\PathFinder,
	Appfuel\Kernel\PathFinderInterface,
	Appfuel\View\FileViewTemplate;

/**
 */
class SqlTemplate extends FileViewTemplate
{
	/**
	 * @param	mixed	$file 
	 * @param	PathFinderInterface	$pathFinder
	 * @return	FileViewTemplate
	 */
	public function __construct($file, PathFinderInterface $pathFinder = null)
	{
		if (null === $pathFinder) {
			$pathFinder = new PathFinder(self::getResourceDir());
		}
		$this->setFile($file);
		$this->setViewCompositor(new SqlFileCompositor($pathFinder));
	}
}
