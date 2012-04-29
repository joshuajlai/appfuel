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
namespace Appfuel\Kernel\Startup;

/**
 * Look for the server name and set the base url constant
 */
class UrlTask extends StartupTask 
{
	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		if (! isset($_SERVER['HTTP_HOST'])) {
			return;
		}
		$scheme = isset($_SERVER['HTTPS']) ? 'https': 'http';
		$host   = $_SERVER['HTTP_HOST'];
		$url    = "{$scheme}://$host";
		if (! defined('AF_BASE_URL')) {
			define('AF_BASE_URL', $url);
		}
	}
}
