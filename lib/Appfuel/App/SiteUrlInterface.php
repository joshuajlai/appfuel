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
namespace Appfuel\App;

/**
 * The site url has the following form: 
 * scheme://<base|cdn>/<resource-dir>/<vendor-ns>/<version>/<root>/<file-path>
 */
interface SiteUrlInterface 
{
	/**
	 * @return	string
	 */
	public function getScheme();

	/**
	 * @return	string
	 */
	public function getBase();

	/**
	 * @return	string
	 */
	public function  getResourceDir();

	/**
	 * @return	string
	 */
	public function getVendor();

	/**
	 * @return	string
	 */	
	public function getVersion();

	/**
	 * @return	string
	 */
	public function getRelativeRoot();
}
