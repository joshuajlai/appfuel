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
 * Adds sorting based on yui3 after property
 */
class AppfuelAdapter implements ResourceAdapterInterface
{
	/**
	 * @param	string
	 * @return	array
	 */
	public function getLayerData($name)
	{
		return ResourceTree::getLayer('appfuel', $name);
	}

	/**
	 * @param	string	$name
	 * @return	ResourceLayer
	 */
	public function buildLayer($layerName)
	{
		$data  = $this->getLayerData($layerName);
		$stack = $this->createFileStack();
		if (! isset($data['pkg']) || ! is_array($data['pkg'])) {
			$err = "could not build layer: no -(pkg) defined";
			throw new DomainException($err);
		}
		$list = $data['pkg'];
		if (! isset($data['filename'])) {
			$err = "could not build layer -($name): -(filename) not defined";
			throw new DomainException($err);
		}
		
		$vendor = $this->createVendor();
		$layer  = $this->createLayer($layerName, $this->createVendor());
		$layer->setFilename($data['filename']);

		foreach ($list as $pkg) {
			$name = $vendor->getVendorName();
			$path = $vendor->getPackagePath();
			$this->resolve($pkg, $name, $path, $stack);
		}
		$layer->setFilestack($stack);

		return $layer;
	}

	/**
	 * @param	string	$pkgName
	 * @param	Yui3StackInterface $stack
	 * @return	null
	 */
	public function resolve($pkgName, $vendor, $path, FileStackInterface $stack)
	{
        $pkgName = new PkgName($pkgName, $vendor);
		$pkg = ResourceTree::findPackage($pkgName);
		if (false === $pkg) {
			$err = "can not resolve dependecies -($pkgName) not found";
			throw new DomainException($err);
		}
        $manifest = $this->createManifest($pkg);
        if ($manifest->isRequiredPackages()) {
            $list = $manifest->getRequiredPackages();
            foreach ($list as $requiredName) {
                $this->resolve($requiredName, $vendor, $path, $stack);
            }
        }
		
		$js  = $manifest->getFiles('js', $path);
		if (! $js) {
			$js = array();
		}	
		$css = $manifest->getFiles('css');
		if (! $css) {
			$css = array();
		}

		$stack->load(array('js' => $js, 'css' => $css));
	}

	/**
	 * @param	array	$data
	 * @return	Yui3Manifest
	 */
	public function createManifest(array $data)
	{
		$type = $data['type'];
		switch ($data['type']) {
			case 'app-view': 
			case 'ui-kit':
				$manifest = new AppViewManifest($data);
				break;
			default :
				$manifest = new AppfuelManifest($data);
		}

		return $manifest;
	}

	/**
	 * @return	Yui3FileStack
	 */
	public function createFileStack()
	{
		return new FileStack();
	}

	/**
	 * @return	VendorInterface
	 */
	public function createVendor()
	{
		$vendor = 'appfuel';
		return new Vendor(array(
			'name'    => $vendor,
			'version' => ResourceTree::getVersion($vendor),
			'path'    => ResourceTree::getPath($vendor)
		));
	}

	/**
	 * @param	$name	
	 * @param	$vendor
	 * @return	VendorInterface
	 */
	public function createLayer($name, $vendor)
	{
		return new AppfuelLayer($name, $vendor);
	}
}
