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
 * A value object used to describe the manifest.json in the package directory
 */
class AppViewManifest extends AppfuelManifest
{
	/**
	 * Name of the chrome package used to configure the html page
	 * @var string
	 */
	protected $htmlPage = null;
	
	/**
	 * Template file used for the page markup
	 * @var string
	 */
	protected $markup = null;

	/**
	 * Template file used for javascript initialization
	 * @var string
	 */
	protected $jsInit = null;

	/**
	 * List of layers to include
	 * @var array
	 */
	protected $layers = array();

	/**
	 * @param	array $data	
	 * @return	PackageManifest
	 */
	public function __construct(array $data)
	{
		parent::__construct($data);

		if (isset($data['html-page'])) {
			$this->setHtmlPage($data['html-page']);
		}

		if (isset($data['markup'])) {
			$this->setMarkupFile($data['markup']);
		}

		if (isset($data['js-init'])) {
			$this->setJsInitFile($data['js-init']);
		}

		if (isset($data['layers'])) {
			$this->setLayers($data['layers']);
		}
	}

	/**
	 * @return	string
	 */
	public function getHtmlPage()
	{
		return $this->htmlPage;
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
		return $this->jsInit;
	}

	/**
	 * @return	bool
	 */
	public function isJsInitFile()
	{
		return is_string($this->jsInit) && ! empty($this->jsInit);
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
	protected function setHtmlPage($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'html page package name  must be a none empty string';
			throw new InvalidArgumentException($err);
		}
		$this->htmlPage = $name;
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

		$this->markup = $file;
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

		$this->jsInit = $file;
	}

	/**
	 * @param	array	$layers
	 * @return	null
	 */
	protected function setLayers(array $layers)
	{
		foreach ($layers as $vendor => $data)  {
			if (! is_string($vendor) || empty($vendor)) {
				$err = "can not set layers: vendor key must be valid string";
				throw new InvalidArgumentException($err);
			}
		}

		$this->layers = $layers;
	}
}
