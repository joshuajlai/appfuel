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
interface ResourceLayerInterface 
{
	/**
	 * @return	string
	 */
	public function getLayerName();

	/**
	 * @param	string	$name
	 * @return	ResourceLayer
	 */
	public function setLayerName($name);

	/**
	 * @return	VendorInterface
	 */
	public function getVendor();

	/**
	 * @param	string	$name
	 * @return	ResourceLayer
	 */
	public function setVendor(VendorInterface $vendor);

	/**
	 * @return	string
	 */
	public function getFilename();

	/**
	 * @param	string	$name
	 * @return	Yui3Layer
	 */
	public function setFilename($name);

	/**
	 * @return	bool
	 */
	public function isFilename();
}
