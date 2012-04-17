<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Html\Resource;

use InvalidArgumentException,
	RecursiveIteratorIterator,
	RecursiveDirectoryIterator,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileWriter,
	Appfuel\Filesystem\FileFinderInterface;

/**
 * Write a resource tree to disk
 */
class ResourceTreeWriter implements ResourceTreeWriterInterface
{
	/**
	 * Default location of the json file that describes all the vendors
	 * @var string
	 */
	protected $defaultTreeFile = 'app/resource-tree.json';

	/**
	 * @param	string	$path	
	 * @param	bool	$isBasePath
	 * @return	TreeBuilder
	 */
	public function writeTree(array $tree, $path = null, $isBasePath = true)
	{
		if (null === $path) {
			$path = $this->getDefaultTreeFile();
		}
		else if (! is_string($path) || empty($path)) {
			$err = 'path to vendors json file must be a non empty string';
			throw new InvalidArgumentException($path);
		}

		$finder = new FileFinder($path, $isBasePath);
		$writer = new FileWriter(new FileFinder($path, $isBasePath));
		$data   = json_encode($tree);

		return $writer->putContent($data, null);	
	}

	/**
	 * @return	string
	 */
	public function getDefaultTreeFile()
	{
		return $this->defaultTreeFile;
	}
}
