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

use InvalidArgumentException;

/**
 * File list that only accepts assets. Also allows you to extend the list of
 * valid extensions. 
 */
class AssetList extends FileList
{
	/**
	 * @return	AssetList
	 */
	public function __construct() 
	{
		$whiteList = array(
			'jpg',
			'png',
			'gif',
			'swf',
			'ico',
		);
		parent::__construct('asset', $whiteList);
	}

	/**
	 * @param	string	$ext
	 * @return	AssetList
	 */
	public function addAssetExtension($ext)
	{
		if (! is_string($ext) || !($ext = trim($ext))) {
			$err = 'asset extension must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (! in_array($ext, $this->whiteList, true)) {
			$this->whiteList[] = $ext;
		}

		return $this;
	}
}
