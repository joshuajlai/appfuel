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
namespace Appfuel\App\Resource;

/**
 */
interface ResourceModuleInterface
{
	/**
	 * Marshal the data into the module
	 *
	 * @param array	$data
	 * @return	null
	 */
	public function load(array $data);

	/**
	 * @return	string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return	bool
	 */
	public function isTheme();

	/**
	 * @return bool
	 */
	public function isGroup();

	/**
	 * @return bool
	 */
	public function isDependencies();

	/**
	 * @return array
	 */
	public function getLang();

	/**
	 * @return bool
	 */
	public function isLang();

	/**
	 * @return array
	 */
	public function getAfter();

	/**
	 * @return bool
	 */
	public function isAfter();
}
