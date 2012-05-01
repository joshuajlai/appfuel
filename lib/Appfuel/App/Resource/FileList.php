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
namespace Appfuel\App\Resource;

use InvalidArgumentException;

/**
 */
class FileList implements FileListInterface
{
	/**
	 * Type of files allowed in this list
	 * @var string
	 */
	protected $type = null;

	/**
	 * white list of allowed extensions
	 * @var array
	 */
	protected $whiteList = array();

	/**
	 * List of file paths
	 * @var	array
	 */
	protected $files = array();

	/**
	 * @param	string	$type
	 * @param	array	$files
	 * @return	FileList
	 */
	public function __construct($type, array $ext = null) 
	{
		$this->setType($type);
		
		if (null !== $ext) {
			$this->setFileExtWhiteList($ext);
		}	
	}

	/**
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return	int
	 */
	public function count()
	{
		return count($this->files);
	}

	/**
	 * @return	array
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * @param	string	$file
	 * @return	FileList
	 */
	public function addFile($file)
	{
		if (! is_string($file) || !($file = trim($file))) {
			$err = 'file must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if ($this->isValidationEnabled()) {
			$pos = strrpos($file, '.');
			if (false === $pos) {
				$err = 'file must have an ext non found';
				throw new InvalidArgumentException($err);
			}
			$ext = substr($file, $pos+1);
			if (! $this->isExtAllowed($ext)) {
				$err = "file ext -($ext) is not allowed";
				throw new InvalidArgumentException($err);  
			}
		}

		if (! in_array($file, $this->files)) {
			$this->files[] = $file;
		}

		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	FileList
	 */
	public function loadFiles(array $list)
	{
		foreach ($list as $file) {
			$this->addFile($file);
		}

		return $this;
	}

	/**
	 * @return	int | null on failure
	 */
	public function key()
	{
		return key($this->files);
	}

	/**
	 * @return	string | null when not found
	 */
	public function current()
	{
		return current($this->files);
	}

	/**
	 * @return	null
	 */
	public function next()
	{
		next($this->files);
	}

	/**	
	 * @return	null
	 */
	public function rewind()
	{
		reset($this->files);
	}

	/**
	 * @return	bool
	 */
	public function valid()
	{
		$key = $this->key();
		if (isset($this->files[$key]) && is_string($this->files[$key])) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string
	 * @return	FileList
	 */
	protected function setType($type)
	{
		if (! is_string($type) || empty($type)) {
			$err = 'file type must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->type = $type;
		return $this;
	}

	/**
	 * @param	string	$ext
	 * @return	bool
	 */
	protected function isExtAllowed($ext)
	{
		if (! is_string($ext) || !in_array($ext, $this->whiteList, true)) {
			return false;
		}

		return true;
	}

	/**
	 * When no extension are in the white list it means we don't want to 
	 * check for extensions at all
	 *
	 * @return	bool
	 */
	protected function isValidationEnabled()
	{
		return count($this->whiteList) > 0;
	}

	/**
	 * @param	array	$list
	 * @return	FileList
	 */
	protected function setFileExtWhiteList(array $list)
	{
		$err = 'file extension must be a none empty string';
		foreach ($list as $ext) {
			if (! is_string($ext) || empty($ext)) {
				throw new InvalidArgumentException($err);
			}
		}

		$this->whiteList = $list;
		return $this;
	}
}
