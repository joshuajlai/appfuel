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
 * Value object used to describe the vendor information
 */
interface VendorInterface
{
	/**
	 * @return	string
	 */
	public function getVendorName();

	/**
	 * @return	string
	 */
	public function getPackagePath();

    /**
     * @return  string
     */
    public function getVersion();

	/**
	 * @return	string
	 */
	public function getPackageTreePath();

}
