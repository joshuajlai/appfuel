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
namespace Appfuel\Orm\DbSource;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\View\Compositor\FileCompositorInterface;

/**
 */
interface SqlFileCompositorInterface
{
	/**
	 * @param	string	$key
	 * @return	bool
	 */
	public function isDbMap($key);

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	public function isTableMap($key);

	/**
	 * @param	string	$key
	 * @return	DbTableMapInterface
	 */
	public function getTableMap($key);

	/**
	 * @param	string	$key	
	 * @param	string	$member
	 * @return	string
	 */
	public function mapColumn($key, $member);

	public function mapColumns($key, $list = null);
}
