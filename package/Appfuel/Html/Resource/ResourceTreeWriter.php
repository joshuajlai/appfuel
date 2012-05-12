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
	 * @var string
	 */
	protected $error = null;

	/**
	 * Default location of the json file that describes all the vendors
	 * @var string
	 */
	protected $defaultTreeFile = 'app/resource-tree.json';

	/**
	 * @var string
	 */
	protected $status = null;

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
			$this->setError($err);
			return false;
		}

		$finder = new FileFinder($path, $isBasePath);
		$writer = new FileWriter(new FileFinder($path, $isBasePath));
		$data   = json_encode($tree);
		if (false === $data) {
			$this->setError(json_last_error());
			return false;
		}

		$full = $finder->getPath();
		$size = $writer->putContent($data, null);
		if (false === $size) {
			$err = "could not write data to -($full)";
			$this->setError($err);
			return false;
		}
		
		$status = "data -($size bytes) written to -($full)";
		$this->setStatus($status);
		return true;
	}

	/**
	 * @return	string
	 */
	public function getDefaultTreeFile()
	{
		return $this->defaultTreeFile;
	}

	/**
	 * @return	bool
	 */
	public function isError()
	{
		return ! empty($this->error);
	}

	/**
	 * @return	string | null when not set
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return	string	| null when not set
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param	string	$msg
	 * @return	null
	 */
	protected function setError($msg)
	{
		$this->error = $msg;
	}

	/**
	 * @param	string	$msg
	 * @return	null
	 */
	protected function setStatus($msg)
	{
		$this->status = $msg;
	}
}
