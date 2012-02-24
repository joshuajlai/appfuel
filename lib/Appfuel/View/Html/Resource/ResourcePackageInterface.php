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
namespace Appfuel\View\Html\Resource;

use InvalidArgumentException,
	Appfuel\Filesystem\FileReader,
	Appfuel\Filesystem\FileReaderInterface;

/**
 * 
 */
interface ResourcePackageInterface
{
	/**
	 * @return	FileReaderInterface
	 */
	public function getFileReader();

	/**
	 * @return	ManifestInterface
	 */
	public function getManifest();

	public function getVendor();
}
