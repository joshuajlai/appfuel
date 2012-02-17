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

use InvalidArgumentException;

/**
 * A value object used to describe the manifest.json 
 */
class PackageManifest implements PackageManifestInterface
{
	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * @var string
	 */
	protected $desc = null;

	/**
	 * @var PackageFileListInterface
	 */
	protected $files = null; 
	
	/**
	 * @var PackageFileListInterface
	 */
	protected $tests = null;

	/**
	 * @var array
	 */
	protected $depends = array();

	/**
	 * @param	string|ViewInterface $view	
	 * @param	string				 $htmlDocFile	path to phtml file
	 * @param	HtmlTagFactory		 $factory
	 * @return	HtmlPage
	 */
	public function __construct(array $data)
	{
		if (isset($data['name'])) {
			$this->setPackageName($data['name']);
		}

		if (isset($data['desc'])) {
			$this->setPackageDescription($data['desc']);
		}

		if (! isset($data['files'])) {
			$this->setFileList($this->createPackageFileList());
		}
		else if (is_array($data['files'])) {
			$this->setFiles($data['files']);
		}
		else {
			$err = 'files must be an associative array';
			throw new InvalidArgumentException($err);
		}

		if (! isset($data['tests'])) {
			$this->setTestList($this->createPackageFileList());
		}
		else if (is_array($data['tests'])) {
			$this->setTests($data['tests']);
		}
		else {
			$err = 'tests must be an associative array';
			throw new InvalidArgumentException($err);

		}

		if (isset($data['depends'])) {
			$this->setDependencies($data['depends']);
		}
	}

	/**
	 * @return	string
	 */
	public function getPackageName()
	{
		return $this->name;
	}

	/**
	 * @return	string
	 */
	public function getPackageDescription()
	{
		return $this->desc;
	}
	
	/**
	 * @return	string
	 */
	public function getAllFiles()
	{
		return $this->getFileList()
					->getAll();
	}

	/**
	 * @return	array | false when type does not exist
	 */
	public function getFiles($type)
	{
		return $this->getFileList()
					->get($type);
	}

	/**
	 * @return	array | false when type does not exist
	 */
	public function getTestFiles($type)
	{
		return $this->getTestList()
					->get($type);
	}

	/**
	 * @return	array
	 */
	public function getAllTestFiles()
	{
		return $this->getTestList()
					->getAll();
	}

	/**
	 * @return	array
	 */
	public function getDependencies()
	{
		return $this->depends;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setPackageName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'package name must be a none empty string';
			throw new InvalidArgumentException($err);
		}
		$this->name = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setPackageDescription($desc)
	{
		if (! is_string($desc)) {
			$err = 'package description must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->desc = $desc;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */	
	protected function setFiles(array $files)
	{
		$list = $this->createPackageFileList();
		$list->set($files);
		$this->setFileList($list);
	}

	/**
	 * @return	PackageFileListInterface
	 */
	protected function getFileList()
	{
		return $this->files;
	}

	/**
	 * @param	PackageFileListInterface	$list
	 * @return	null
	 */
	protected function setFileList(PackageFileListInterface $list)
	{
		$this->files = $list;
	}

	/**
	 * @return	PackageFileListInterface
	 */
	protected function getTestList()
	{
		return $this->tests;
	}

	/**
	 * @param	PackageFileListInterface	$list
	 * @return	null
	 */
	protected function setTestList(PackageFileListInterface $list)
	{
		$this->tests = $list;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */	
	protected function setTests(array $files)
	{
		$list = $this->createPackageFileList();
		$list->set($files);
		$this->setTestList($list);
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	protected function setDependencies(array $list)
	{
		if ($list !== array_values($list)) {
			$err = 'dependency list must be an indexed array of strings';
			throw new InvalidArgumentException($err);
		}

		foreach ($list as $package) {
			if (! is_string($package) || empty($package)) {
				$err = 'package path must be a non empty string';
				throw new InvalidArgumentException($err);
			}
		}

		$this->depends = $list;
	}

	/**
	 * @return	PackageFileList
	 */
	protected function createPackageFileList()
	{
		return new PackageFileList();
	}
}
