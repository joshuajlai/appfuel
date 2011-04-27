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
namespace Appfuel\App\View;

use Appfuel\Framework\View\TemplateInterface;

/**
 * Handles all assignments of data to be used in a template
 */
class Template extends Data implements TemplateInterface
{
	/**
	 * Holds a list of files used by the template. This is not kept
	 * in the dictionary to avoid name collisions with the keys
	 * @var array
	 */
	protected $files = array();

	/**
	 * Detemine if a particular file has been registered and is of the
	 * type Appfuel\App\View\File
	 * 
	 * @return bool
	 */
    public function fileExists($key)
	{
		return array_key_exists($key, $this->files) &&
			   $this->files[$key] instanceof File;
	}

	/**
	 * Create a file object and add it to the array with the given key
	 *
	 * @param	scalar	$key
	 * @param	string	$path
	 * @return	Template
	 */
    public function addFile($key, $path)
	{
		if (! is_scalar($key)) {
			throw new Exception(
				"Add file failed: key must be a scalar value"
			);
		}

		if (! is_string($path) || empty($path)) {
			throw new Exception(
				"Add file failed: path must be a string and not empty"
			);
		}

		/* 
		 * this file class ensures the path is relative 
		 * to base-path/resources
		 */ 
		$this->files[$key] = new File($path);
		return $this;
	}

	/**
	 * @return false | File
	 */
    public function getFile($key)
	{
		if (! $this->fileExists($key)) {
			return false;
		}

		return $this->files[$key];
	}

	/**
	 * @return array
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * Build the template file indicated by key into string. Use data in
	 * the dictionary as scope
	 *
	 * @param	string	$key	template file identifier
	 * @param	array	$data	used for private scope
	 * @return	string
	 */
    public function buildFile($key, array $data = array(), $isPrivate = false)
	{
		if (! $this->fileExists($key)) {
			return '';
		}

		$file = $this->getFile();
		if (! $file->isFile()) {
			$showAbsolute = true;
			$path = $file->getResourcePath($showAbsolute);
			$err = "BuildFile failed: file does not exist at $path";
			throw new Exception($err);
		}
		
		/*
		 * When private use only data in the second parameter. 
		 * When not private and data in second parameter then merge
		 * When not private and no data then use only data in dictionary
		 */
		if (true === $isPrivate) {
			$scope = new Scope($data);
		}
		else if (! empty($data)) {
			$data = array_merge($this->getAll(), $data);
			$scope = new Scope($data);
		}
		else {
			$scope = new Scope($this->getAll());
		}
		
		return $scope->build($file->getRealPath())
	}
}
