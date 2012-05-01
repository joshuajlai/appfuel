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

/**
 * Creates objects needed to traverse appfuel resource dependencies
 */
interface ResourceFactoryInterface
{
	/**
	 * @param	string	$vendor 
	 * @return	ResourceAdapterInterface
	 */
	public function createResourceAdapter();

	/**
	 * @return	FileStackInterface
	 */
	public function createFileStack();

	/**
	 * @param	string	$name
	 * @param	VendorInterface $vendor
	 * @return	ResourceLayerInterface
	 */
	public function createLayer($name, VendorInterface $vendor);

	/**
	 * @param	array	$data
	 * @return	VendorInterface
	 */
	public function createVendor(array $data);

	/**
	 * @param	array	$data
	 * @return	AppfuelManifestInterface
	 */
	public function createPkg(array $data, $vendor = null);
}
