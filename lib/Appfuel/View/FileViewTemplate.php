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
	Appfuel\Kernel\PathFinderInterface,
	Appfuel\View\Compositor\FileCompositor,
	Appfuel\View\Compositor\FileCompositorInterface,
	Appfuel\View\Compositor\ViewCompositorInterface;

/**
 * The file view template uses a FileCompositorInterface to compose a phtml
 * file into string. A 
 */
class FileViewTemplate extends ViewTemplate
{
    /**
     * Relative path to a file template
     * @var string
     */
	protected $file = null;

	/**
	 * @param	mixed	$file 
	 * @param	array	$data
	 * @return	FileTemplate
	 */
	public function __construct($templateFile,
								PathFinderInterface $pathFinder = null,
								 array $data = null)
	{
		if (null === $pathFinder) {
			$pathFinder = new UiPathFinder();
		}
		$this->setFile($templateFile);
		parent::__construct($data, new FileCompositor($pathFinder));
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
		if (empty($file) || ! is_string($file) || ! ($file = trim($file))) {
			$err = 'file path must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->file = $file;
		return $this;
	}

	/**
	 * Used with file templates to change the part of the absolute path 
	 * from the root to the relative. When isBase is true the root path
	 * starts at the end of AF_BASE_PATH.
	 *
	 * @throws	InvalidArgumentException	when path is not a string
	 * @param	string	$path
	 * @return	ViewTemplate
	 */
	public function setRelativeRootPath($path, $isBase = true)
	{
		$compositor = $this->getViewCompositor();
		if ($compositor instanceof FileCompositorInterface) {
			$compositor->setRelativeRootPath($path, $isBase);
		}

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
		$compositor = $this->getViewCompositor();
		if (! ($compositor instanceof ViewCompositorInterface)) {
			$err  = 'build failed: can not build without a view compositor or ';
			$err .= 'view compositor that does not implement a Appfuel\View';
			$err .= '\Compositor\ViewCompositorInterface';
			throw new RunTimeException($err);
		}

		if ($this->templateCount() > 0) {
			$this->buildTemplates();
		}

		if ($this->isFileTemplate()) {
			$file = $this->getFile();
			if (! ($compositor instanceof FileCompositorInterface)) {
				$err  = 'build failed: when a template file is set the view ';
				$err .= 'compositor must implement Appfuel\View\FileCompositor';
				$err .= 'Interface';
				throw new RunTimeException($err);
			}

			$compositor->setFile($file);
		}

		return $compositor->compose($this->getAll());
	}
}
