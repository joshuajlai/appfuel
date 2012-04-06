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

use DomainException,
	InvalidArgumentException;

/**
 */
class PagePkg extends Pkg implements PagePkgInterface
{
	/**
	 * Override member instead of calling setter in constructor to respect
	 * inheritence.
	 * @var string
	 */
	protected $validType = 'page';

	/**
	 * Name of the html document this page belongs to 
	 * @var string
	 */
	protected $htmlDocName = null;
	
	/**
	 * Template file used for the page markup
	 * @var string
	 */
	protected $markup = null;

	/**
	 * Template file used for javascript initialization
	 * @var string
	 */
	protected $init = null;

	/**
	 * List of layers to include
	 * @var array
	 */
	protected $layers = array();

	/**
	 * @param	array $data	
	 * @return	PackageManifest
	 */
	public function __construct(array $data, $vendor = null)
	{
		parent::__construct($data, $vendor);

		$docName = 'htmldoc.default-html';
		if (isset($data['htmldoc'])) {
			$docName = $data['htmldoc'];
		}
		$this->setHtmlDocName($docName, $vendor);

		if (! isset($data['markup'])) {
			$err  = "all html page views must declare a markup file using key ";
			$err .= "-(markup): none found";
			throw new DomainException($err);
		}
		$this->setMarkupFile($data['markup']);

		if (isset($data['init'])) {
			$this->setJsInitFile($data['init']);
		}

		if (isset($data['layers'])) {
			$this->setLayers($data['layers'], $vendor);
		}
	}

	/**
	 * @return	string
	 */
	public function getHtmlDocName()
	{
		return $this->htmldocName;
	}

	/**
	 * @return	string
	 */
	public function getMarkupFile()
	{
		return $this->markup;
	}

	/**
	 * @return	string
	 */
	public function getJsInitFile()
	{
		return $this->init;
	}

	/**
	 * @return	bool
	 */
	public function isJsInitFile()
	{
		return is_string($this->init) && ! empty($this->init);
	}

	/**
	 * @return	string
	 */
	public function getLayers()
	{
		return $this->layers;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setHtmlDocName($name, $vendor = null)
	{
		if (is_string($name)) {
			$docName = new PkgName($name, $vendor);
		}
		else if ($name instanceof PkgNameInterface) {
			$docName = $name;
		}
		else {
			$err  = 'name must be a string or an object that implements ';
			$err .= 'Appfuel\Html\Resource\PkgNameInterface';
			throw new DomainException($err);
		}

		$this->htmldocName = $docName;
	}

	/**
	 * @param	string	$file
	 * @return	null
	 */
	protected function setMarkupFile($file)
	{
		if (! is_string($file) || empty($file)) {
			$err = 'markup file must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$src  = $this->getSourcePath();
		$path = $file;
		if (! empty($src)) {
			$path = "$src/$file";
		}
		$this->markup = $path;
	}

	/**
	 * @param	string	$file
	 * @return	null
	 */
	protected function setJsInitFile($file)
	{
		if (! is_string($file) || empty($file)) {
			$err = 'js init file must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$src = $this->getSourcePath();
		$path = $file;
		if (! empty($src)) {
			$path = "$src/$file";
		}

		$this->init = $path;
	}

	/**
	 * @param	array	$layers
	 * @return	null
	 */
	protected function setLayers(array $layers, $vendor = null)
	{
        $names = array();
        foreach ($layers as $str) {
            if (false === strpos($str, '.')) {
                $str = "layer.$str";
            }
            $names[] = new PkgName($str, $vendor);
        }
        $this->layers = $names;
	}
}
