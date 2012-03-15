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
namespace Appfuel\Html\Resource;

use InvalidArgumentException;

/**
 * Holds a list of files categorized by type. When a file is added it is always 
 * prepended to the top of the stack. This is done because, in an html resource
 * module files have dependencies and dependencies are always added before 
 * their dependent files.
 */
class FileStack implements FileStackInterface
{
	/**
	 * @var array
	 */
	protected $files = array();

	/**
	 * @param	array	$files
	 * @return	FileStack
	 */
	public function __construct(array $files = null)
	{
		if (null !== $files) {
			$this->load($files);
		}
	}

	/**
	 * @return	array
	 */
	public function getAll()
	{
		return $this->files;
	}

	/**
	 * @return	array
	 */
	public function getTypes()
	{
		return array_keys($this->files);
	}

	/**
	 * @param	string	$type
	 * @return	array | false when type does not exist
	 */
	public function get($type)
	{
		if (is_string($type) && isset($this->files[$type])) {
			return $this->files[$type];
		}

		return false;
	}

	/**
	 * @param	string	$type
	 * @param	string	$file
	 * @return	PackageFileList
	 */
	public function add($type, $file)
	{
		if (! is_string($type) || empty($type)) {
			$err = 'file type must be a none empty string';
			throw new InvalidArgumentException($err);
		}

		if (! is_string($file) || empty($file)) {
			$err = 'file path must be a none empty string';
			throw new InvalidArgumentException($err);
		}

		if (! isset($this->files[$type])) {
			$this->files[$type] = array($file);
			return $this;
		}

		if (! in_array($file, $this->files[$type])) {
			array_unshift($this->files[$type], $file);
		}
			
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	PackageFileList
	 */
	public function load(array $list)
	{
		if ($list === array_values($list)) {
			$err = 'file list must be an associative array of type => array';
			throw new InvalidArgumentException($err);
		}

		foreach ($list as $type => $files) {
			if (is_string($files)) {
				$this->add($type, $files);
				continue;
			}

			if (! is_array($files)) {
				$err = 'array structure must be type=>string or type=>array';
				throw new InvalidArgumentException($err);
			}

			foreach ($files as $file) {
					$this->add($type, $file);
			}
		}

		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	PackageFileList
	 */
	public function set(array $list)
	{
		$this->clear();
		$this->load($list);
		return $this;
	}

	/**
	 * @return	PackageFileLIst
	 */
	public function clear($type = null)
	{
		if (null === $type) {
			$this->files = array();
			return $this;
		}

		if (is_string($type) && isset($this->files[$type])) {
			$this->files[$type] = array();
		}

		return $this;
	}
}
