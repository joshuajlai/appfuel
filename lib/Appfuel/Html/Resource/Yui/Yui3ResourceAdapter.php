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
namespace Appfuel\Html\Resource\Yui;

use DomainException,
	Appfuel\Html\Resource\Vendor,
	Appfuel\Html\Resource\ResourceTree;

/**
 * Adds sorting based on yui3 after property
 */
class Yui3ResourceAdapter
{
	/**
	 * @param	string
	 * @return	array
	 */
	public function getLayerData($name)
	{
		return ResourceTree::getLayer('yui3', $name);
	}

	/**
	 * @param	string	$name
	 * @return	ResourceLayer
	 */
	public function buildLayer($name)
	{
		$data  = $this->getLayerData($name);
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
		$layer  = $this->createLayer($name, $this->createVendor());
		$layer->setFilename($data['filename']);

		foreach ($list as $pkg) {
			$this->resolve($pkg, $stack);
		}
		$stack->sortByPriority();
		$layer->setFilestack($stack);

		return $layer;
	}

	/**
	 * @param	string	$pkgName
	 * @param	Yui3StackInterface $stack
	 * @return	null
	 */
	public function resolve($pkgName, Yui3FileStackInterface $stack)
	{
        $pkg = ResourceTree::getPackage('yui3', $pkgName);
		if (false === $pkg) {
			$err = "can not resolve dependecies -($pkgName) not found";
			throw new DomainException($err);
		}
        $manifest = $this->createManifest($pkg);
        $name = $manifest->getPackageName();
        $type  = 'js';
        if ($manifest->isCss()) {
            $type = 'css';
        }

        if ($manifest->hasNoDependencies()) {
            $stack->add($type, $name);
        } else if ($manifest->isUse()) {
            $list = $manifest->getUse();
            foreach ($list as $yuiName) {
                $this->resolve($yuiName, $stack);
            }
        }
        else if ($manifest->isRequire()) {
            $list = $manifest->getRequire();
            foreach ($list as $yuiName) {
                $this->resolve($yuiName, $stack);
            }
            $stack->add($type, $name);
        }

        if ($manifest->isAfter()) {
            $afterList = $manifest->getAfter();
            foreach ($afterList as $afterName) {
                $stack->addAfter($type, $name,  $afterName);
            }
        }
	}

	/**
	 * @param	array	$data
	 * @return	Yui3Manifest
	 */
	public function createManifest(array $data)
	{
		return new Yui3Manifest($data);
	}

	/**
	 * @return	Yui3FileStack
	 */
	public function createFileStack()
	{
		return new Yui3FileStack();
	}

	/**
	 * @return	VendorInterface
	 */
	public function createVendor()
	{
		$vendor = 'yui3';
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
		return new Yui3Layer($name, $vendor);
	}
}
