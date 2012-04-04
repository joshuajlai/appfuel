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
namespace Appfuel\View;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Html\Resource\PkgName;

/**
 * The file view template uses a FileCompositorInterface to compose a phtml
 * file into string. A 
 */
class FileTemplate extends ViewTemplate implements TemplateInterface
{
    /**
     * Relative path to a file template
     * @var string
     */
	protected $file = null;

	/**
	 * @var PkgNameInterface
	 */
	protected $pkgName = null;

	/**
	 * Flag used to determine if the file path is actually a package name
	 * @var bool
	 */
	protected $isPkg = null;

	/**
	 * @param	string $file 
	 * @return	FileViewTemplate
	 */
	public function __construct($file, $isPkg = false, $default = null)
	{
		if (true === $isPkg) {
			$this->setPackage($file, $default);
		}
		else {
			$this->setFile($file);
		}
	}

	/**
	 * Relative file path to template file
	 * @return	null
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param	string	$file
	 * @return	ViewTemplate
	 */
	public function setFile($file)
	{
		if (! is_string($file) || empty($file)) {
			$err = 'file path or pkg name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->file    = $file;
		$this->pkgName = null;
		return $this;
	}

	/**
	 * @return	PkgNameInterface
	 */
	public function getViewPkgName()
	{
		return $this->pkgName;
	}

	/**
	 * @param	PkgNameInterface $name
	 * @return	FileTemplate
	 */
	public function setViewPkgName(PkgNameInterface $name)
	{
		$this->pkgName = $name;
		$this->file    = null;
		return $this;
	}

	/**
	 * @param	string	$name
	 * @param	string	$defaultVendor
	 * @return	PkgName
	 */
	public function createViewPkgName($name, $defaultVendor = null)
	{
		return new PkgName($name, $defaultVendor);
	}

	/**
	 * @return	bool
	 */
	public function isViewPackage()
	{
		return	$this->pkgName instanceof PkgNameInterface;
	}

	/**
	 * @param	string	$name 
	 * @param	string	$defaultVendor
	 * @return	FileTemplate
	 */
	public function setViewPackage($name, $defaultVendor = null)
	{
		if (null === $defaultVendor) {
			$defaultVendor = 'appfuel';
		}

		$this->setViewPkgName($this->createViewPkgName($name, $defaultVendor));
		return $this;
	}

	/**
	 * Build the template file indicated by key into string. Use data in
	 * the dictionary as scope
	 *
	 * @param	string	$key	template file identifier
	 * @param	array	$data	used for private scope
	 * @return	string
	 */
    public function build()
	{
		$data = $this->getAll();
		if ($this->isViewPackage()) {
			$name   = $this->getViewPkgName();
			$result = ViewCompositor::composePackage($name, $data);
		}
		else {
			$file   = $this->getFile();
			$result = ViewCompositor::composeFile($file, $data);
		}

		return $result;
	}
}
