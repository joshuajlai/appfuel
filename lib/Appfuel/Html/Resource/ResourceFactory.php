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

use DomainException;

/**
 * Creates objects needed to traverse appfuel resource dependencies
 */
class ResourceFactory implements ResourceFactoryInterface
{
	/**
	 * @param	string	$vendor 
	 * @return	ResourceAdapterInterface
	 */
	public function createAdapter()
	{
		return new ResourceAdapter();
	}

	/**
	 * @return	FileStackInterface
	 */
	public function createFileStack()
	{
		return new FileStack();
	}

	/**
	 * @param	string	$name
	 * @param	VendorInterface $vendor
	 * @return	ResourceLayerInterface
	 */
	public function createLayer($name, VendorInterface $vendor)
	{
		return new ResourceLayer($name, $vendor);
	}

	/**
	 * @param	array	$data
	 * @return	VendorInterface
	 */
	public function createVendor(array $data)
	{
		return new Vendor($data);
	}

	/**
	 * @param	array	$data
	 * @return	AppfuelManifestInterface
	 */
	public function createManifest(array $data)
	{
		if (! isset($data['type'])) {
			$err = 'manifest must have a type property';
			throw new DomainException($err);
		}
		$type = $data['type'];
		
		switch ($type) {
			case 'app-view':
			case 'ui-kit':
				$manifest = new ViewManifest($data);
				break;
			case 'chrome':
				$manifest = new ChromeManifest($data);
				break;
			case 'pkg':
				$manifest = new PkgManifest($data);
				break;
			default:
				$err = "invalid manifest: no strategy for -($type)";
				throw new DomainException($err);
		}

		return $manifest;
	}
}
