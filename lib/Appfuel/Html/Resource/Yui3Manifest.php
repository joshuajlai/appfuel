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
namespace Appfuel\Html\Resource;

use InvalidArgumentException;

/**
 * A value object used to describe the manifest.json in the package directory
 */
class Yui3Manifest implements Yui3ManifestInterface
{
	/**
	 * Name of this packages build file. 
	 * @var string
	 */
	protected $name = null;

	/**
	 * List of packages this package depends on
	 * @var array
	 */	
	protected $requires = array();

	/**
	 * List of packages this package uses. When this is enabled this package
	 * has no files of its own.
	 * Note: its requires or use or none, but not require and use.
	 * @var array 
	 */
	protected $uses = array();

	/**
	 * Have not figured this one out yet. I think this package must come after
	 * the packages listed.
	 * @var array
	 */
	protected $after = array();

	/**
	 * List of languages supported by this pacakge
	 * @var array
	 */
	protected $lang = array();

	/**
	 * Flag used to determine if this package is skinnable
	 * @var bool
	 */
	protected $isSkinnable = false;

	/**
	 * Flag used to determine if this is purely css
	 * @var bool
	 */
	protected $isCss = false;

	/**
	 * @param	array $data	
	 * @return	PackageManifest
	 */
	public function __construct(array $data)
	{
        if (! isset($data['name'])) {
            $err = 'package name not found an must exist';
            throw new InvalidArgumentException($err);
        }
        $this->setPackageName($data['name']);

		if (isset($data['requires'])) {
			$this->setRequire($data['requires']);
		}
		else if (isset($data['use'])) {
			$this->setUse($data['use']);
		}

		if (isset($data['after'])) {
			$this->setAfter($data['after']);
		}

		if (isset($data['lang'])) {
			$this->setLang($data['lang']);
		}

		if (isset($data['skinnable']) && $data['skinnable']) {
			$this->isSkinnable = true;
		}

		if (isset($data['type']) && 'css' === $data['type']) {
			$this->isCss = true;
		}
	}

	/**
	 * @return	string
	 */
	public function getPackageName()
	{
		return $this->name;
	}

	/**
	 * @return	array
	 */
	public function getRequire()
	{
		return $this->requires;
	}

	/**
	 * @return	bool
	 */
	public function isRequire()
	{
		return ! empty($this->requires);
	}

	/**
	 * @return	array
	 */
	public function getUse()
	{
		return $this->uses;
	}

	/**
	 * @return	bool
	 */
	public function isUse()
	{
		return ! empty($this->uses);
	}

	/**
	 * @return array
	 */
	public function getAfter()
	{
		return $this->after;	
	}

	/**
	 * @return	bool
	 */
	public function isAfter()
	{
		return ! empty($this->after);
	}

	/**
	 * @return	array
	 */
	public function getLang()
	{
		return $this->lang;
	}

	/**
	 * @return	bool
	 */
	public function isLang()
	{
		return ! empty($this->lang);
	}

	/**
	 * @return	bool
	 */
	public function isSkinnable()
	{
		return $this->isSkinnable;
	}

	/**
	 * @return	bool
	 */
	public function isCss()
	{
		return $this->isCss;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setPackageName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'package name must be a none empty string';
			throw new InvalidArgumentException($err);
		}
		$this->name = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setRequire(array $list)
	{
		if (! $this->isValidList($list)) {
			$err = "can not set require pkg name must be a valid string";
			throw new InvalidArgumentException($err);
		}

		$this->requires = $list;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setUse(array $list)
	{
		if (! $this->isValidList($list)) {
			$err = "can not set use pkg name must be a valid string";
			throw new InvalidArgumentException($err);
		}

		$this->uses = $list;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setAfter(array $list)
	{
		if (! $this->isValidList($list)) {
			$err = "can not set after pkg name must be a valid string";
			throw new InvalidArgumentException($err);
		}

		$this->after = $list;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setLang(array $list)
	{
		if (! $this->isValidList($list)) {
			$err = "can not set lang name must be a valid string";
			throw new InvalidArgumentException($err);
		}

		$this->lang = $list;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function isValidList(array $list)
	{
		foreach ($list as $name) {
			if (! is_string($name) || empty($name)) {
				return false;
			}
		}

		return true;
	}
}
