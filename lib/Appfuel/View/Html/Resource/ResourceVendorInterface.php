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
namespace Appfuel\View\Html\Resource;

/**
 * Value object used to describe the vendor information and package list
 */
interface ResourceVendorInterface
{
	/**
	 * @return	string
	 */
	public function getVendorName();

	/**
	 * @return	string
	 */
	public function getVendorDescription();
	
	/**
	 * @return	string
	 */
	public function getPackagePath();

	/**
	 * @return	string
	 */
	public function getBuildPath();

    /**
     * @return  string
     */
    public function getVersion();

	/**
	 * @return array
	 */
	public function getPackages();
}
