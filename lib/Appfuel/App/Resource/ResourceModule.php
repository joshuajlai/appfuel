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
 * The resource module is a value object holding details of the module
 */
class ResourceModule implements ResourceModuleInterface
{
	/**
	 * Name of the module
	 * @var string
	 */
	protected $name = null;

	/**
	 * Type of files this module holds, css or js
	 * @var string
	 */
	protected $type = 'js';

	/**
	 * When module group is true the module itself is not considered a 
	 * resource and you wont need to look for its file, instead just resolve
	 * all its dependencies
	 * @var bool
	 */
	protected $isGroup = false;

	/**
	 * Flag used to determine if the module contains any theme resources
	 * @var bool
	 */
	protected $isTheme = false;

	/**
	 * List of modules this module depends on
	 * @var array
	 */	
	protected $depends = array();

	/**
	 * List of support languages
	 * @var array
	 */
	protected $lang = array();

	/**
	 * List of modules that should be listed after this module
	 * @var array
	 */
	protected $after = array();

	/**
	 * @param	array $data
	 * @return	ResourceModule
	 */
	public function __construct(array $data = null)
	{
		if (null !== $data) {
			$this->load($data);
		}
	}

	/**
	 * @param	array	$data
	 * @return	null
	 */
	public function load(array $data)
	{
		if (empty($data)) {
			$err = 'module data can not be empty';
			throw new InvalidArgumentException($err);
		}

		if (! isset($data['name'])) {
			$err = 'module name not found with key -(name) and is required';
			throw new InvalidArgumentException($err);
		}
		$this->setName($data['name']);

		if (isset($data['type']) && 
			is_string($data['type']) &&
			'css' === strtolower($data['type'])) {
			$this->setTypeToCss();
		}

		if (isset($data['skinnable']) && true === $data['skinnable']) {
			$this->enableTheme();
		}

		$isDepend = isset($data['requires']);
		$dependencies = (true === $isDepend) ? $data['requires'] : null;
		if (isset($data['is_group']) && true === $data['is_group']) { 
			$this->setDependencies($dependencies);
			$this->useModuleAsGroup();	
		}
		else if (true === $isDepend) {
			$this->setDependencies($dependencies);
		}

		if (isset($data['lang'])) {
			$this->setLang($data['lang']);
		}

		if (isset($data['after'])) {
			$this->setAfter($data['after']);
		}
	}

	/**
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return	bool
	 */
	public function isTheme()
	{
		return $this->isTheme;	
	}

	/**
	 * @return bool
	 */
	public function isGroup()
	{
		return $this->isGroup;
	}

	/**
	 * @return bool
	 */
	public function isDependencies()
	{
		return count($this->depends) > 0;
	}

	/**
	 * @return	array
	 */
	public function getDependencies()
	{
		return $this->depends;
	}

	/**
	 * @return array
	 */
	public function getLang()
	{
		return $this->lang;
	}

	/**
	 * @return bool
	 */
	public function isLang()
	{
		return count($this->lang) > 0;
	}

	/**
	 * @return array
	 */
	public function getAfter()
	{
		return $this->after;
	}

	/**
	 * @return bool
	 */
	public function isAfter()
	{
		return count($this->after) > 0;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setName($name)
	{
		if (!is_string($name) || ! ($name = trim($name))) {
			$err = 'module name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->name = $name;
	}

	/**
	 * @return null
	 */
	protected function setTypeToCss()
	{
		$this->type = 'css';
	}

	/**
	 * @return null
	 */
	protected function setTypeToJs()
	{
		$this->type = 'js';
	}

	/**
	 * @return null
	 */
	protected function enableTheme()
	{
		$this->isTheme = true;
	}

	/**
	 * @return null
	 */
	protected function disableTheme()
	{
		$this->isTheme = false;
	}

	/**
	 * @param	array $data
	 * @return	null
	 */
	protected function setDependencies(array $data)
	{
		$err  = 'modules names in the dependency list must be none empty ';
		$err .= 'strings';
		foreach ($data as $module) {
			if (! is_string($module) || ! ($module = trim($module))) {
				throw new InvalidArgumentException($err);	
			}
		}

		$this->depends = $data;
	}

	/**
	 * @return null
	 */
	protected function useModuleAsGroup()
	{
		$this->isGroup = true;
	}

	/**
	 * @param	array	$lang
	 * @return	null
	 */
	protected function setLang(array $lang)
	{
		$err = 'all lang codes must be a non empty string';
		foreach ($lang as $codes) {
			if (! is_string($codes) || ! ($code = trim($code))) {
				throw new InvalidArgumentException($err);	
			}
		}

		$this->lang = $lang;
	}

	/**
	 * @param	array	$after
	 * @return	null
	 */
	protected function setAfter(array $after)
	{
		$err = 'all module names must be a non empty string';
		foreach ($after as $name) {
			if (! is_string($name) || ! ($name = trim($name))) {
				throw new InvalidArgumentException($err);	
			}
		}

		$this->after = $after;
	}


}
