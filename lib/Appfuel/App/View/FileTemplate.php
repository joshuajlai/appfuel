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

use Appfuel\Framework\Exception,
	Appfuel\Framework\FileInterface,
	Appfuel\Framework\View\FileTemplateInterface;

/**
 * Handles all assignments of data to be used in a template
 */
class FileTemplate extends Data implements FileTemplateInterface
{
	/**
	 * Holds a list of files used by the template. This is not kept
	 * in the dictionary to avoid name collisions with the keys
	 * @var array
	 */
	protected $files = array();

	/**
	 * Detemine if a particular key has a path string or object that 
	 * implements the FileInteface
	 * 
	 * @return bool
	 */
    public function fileExists($key)
	{
		if (! array_key_exists($key, $this->files)) {
			return false;
		}
			   
		return is_string($this->files[$key]) || 
			   $this->files[$key] instanceof FileInterface;
	}

	/**
	 * Store a path string for file interface in an associative array that
	 * can be retrieved by the key
	 *
	 * @param	scalar	$key
	 * @param	string	$path
	 * @return	Template
	 */
    public function addFile($key, $path)
	{
		if (! is_scalar($key) || empty($key)) {
			throw new Exception(
				"addFile failed: key must be a scalar value"
			);
		}
		
		if (empty($path)) {
			throw new Exception("addFile failed: path can not be empty");
		}
		
		if (is_string($path) ||  ($path instanceof FileInterface)) {
			$this->files[$key] = $path;
		} 
		else {
			throw new Exception(
				"addFile faild: path must be a string or file interface"
			); 
		}	
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

	public function createClientsideFile($path)
	{
		return new ClientsideFile($path);
	}

	/**
	 * Build the template file indicated by key into string. Use data in
	 * the dictionary as scope
	 *
	 * @param	string	$key	template file identifier
	 * @param	array	$data	used for private scope
	 * @return	string
	 */
    public function buildFile($key, $data = null, $isPrivate = false)
	{
		if (! $this->fileExists($key)) {
			return '';
		}

		$file = $this->getFile($key);

		/* convert this path string to a file */
		if (is_string($file)) {
			$file = $this->createClientsideFile($file);
		}

		if (! $file->isFile()) {
			$path = $file->getFullPath();
			$err = "BuildFile failed: file does not exist at $path";
			throw new Exception($err);
		}
			
		$isPrivate = (bool) $isPrivate;
	
		/*
		 * When private use only data in the second parameter. 
		 * When not private and data in second parameter then merge
		 * When not private and no data then use only data in dictionary
		 */
		if (true === $isPrivate) {
			$scopeData = $data;
		}
		else if (is_array($data) && ! empty($data)) {
			$scopeData = array_merge($this->getAll(), $data);
		}
		else {
			$scopeData = $this->getAll();
		}
	
		$scope = new Scope($scopeData);	
		return $scope->build($file->getRealPath());
	}

	public function build($data = null)
	{
		
	}
}
