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

use DomainException;

/**
 * A value object used to describe the manifest.json in the pkg directory
 */
class ViewPkg extends Pkg implements ViewPkgInterface
{
	/**
	 * @var string
	 */
	protected $markup = null;

	/**
	 * @var bool
	 */
	protected $isJsView = false;	
	/**
	 * @param	array $data	
	 * @return	PackageManifest
	 */
	public function __construct(array $data, $vendor = null)
	{
		$this->setValidType('view');
		parent::__construct($data, $vendor);

		if (! isset($data['markup'])) {
			$err = 'for any view pacakge the markup file must be set';
			throw new DomainException($err);
		}
		$this->setMarkupFile($data['markup']);

		if (isset($data['is-jsview']) && true === $data['is-jsview']) {
			$this->isJsView = true;
		}
	}

	/**
	 * @return	string
	 */
	public function getMarkupFile($path = null)
	{
		$markup = $this->markup;
		if (is_string($path) && ! empty($path)) {
			$markup = "$path/$markup";
		}

		return $markup;
	}

	/**
	 * @return	string
	 */
	public function isJsView()
	{
		return $this->isJsView;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setMarkupFile($file)
	{
		if (! is_string($file) || empty($file)) {
			$err = 'markup file path must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$src  = $this->getSourcePath();
		$path = $file;
		if (! empty($src)) {
			$path = "$src/$path";
		} 
		$this->markup = $path;
	}
}
