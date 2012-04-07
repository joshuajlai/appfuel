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
class HtmlDocPkg extends PagePkg implements HtmlDocPkgInterface
{
	/**
	 * Override member property instead of using setter so that we can
	 * respect inheritence of the constructor
	 * @var string
	 */
	protected $validType = 'htmldoc';

	/**
	 * Name of the html configuration pkg 
	 * @var string
	 */
	protected $config = null;
	
	/**
	 * @param	array $data	
	 * @return	PackageManifest
	 */
	public function __construct(array $data, $vendor = null)
	{
		parent::__construct($data, $vendor);

		$htmldoc = 'htmldoc.' . $this->getName();
		$this->setHtmlDocName($htmldoc, $vendor);

		$config = array();
		if (isset($data['html'])) {
			$config = $data['html'];
		}
		$this->setHtmlConfig($config);
	}

	/**
	 * @return	string
	 */
	public function getHtmlConfig()
	{
		return $this->htmlConfig;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setHtmlConfig(array $config)
	{
		$this->htmlConfig = $config;
	}
}
