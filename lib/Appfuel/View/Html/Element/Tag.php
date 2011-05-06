<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\View\Html\Element;

use Appfuel\App\View\Data;

/**
 *
 */
class Tag extends Data
{
	/**
	 * Used to separate content
	 * @var string
	 */
	protected $separator = ' ';

	/**
	 * White list of accepted attributes
	 * @var array
	 */
	protected $validAttrs = array();

	/**
	 * Dictionary used to hold html tag attributes
	 * @var array
	 */
	protected $attrs = array();

	/**
	 * Flag used to determine if a tag should validate
	 * the attributes that we assigned to it
	 * @var bool
	 */
	protected $isAttrValidation = true;

	/**
	 * Flag used to determine if attributes will be used in this tag
	 * @var bool
	 */
	protected $isAttrs = true;

	/**
	 * @return string
	 */
	public function getSeparator()
	{
		return $this->separator;
	}

	/**
	 * @param	scalar	$char
	 * @return	Tag
	 */
	public function setSeparator($char)
	{
		if (! is_scalar($char)) {
			return $this;
		}

		$this->separator = $char;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAttributesEnabled()
	{
		return $this->isAttrs;
	}

	/**
	 * @return Tag
	 */
	public function enableAttributes()
	{
		$this->isAttrs = true;
		return $this;
	}

	/**
	 * @return Tag
	 */
	public function disableAttributes()
	{
		$this->isAttrs = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAttributeValidation()
	{
		return $this->isAttrValidation;
	}

	/**
	 * @return Tag
	 */
	public function enableAttributeValidation()
	{
		$this->isAttrValidation = true;
		return $this;
	}

	/**
	 * @return Tag
	 */
	public function disableAttributeValidation()
	{
		$this->isAttrValidation = false;
		return $this;
	}

	/**
	 * Add a valid attribute to the white list
	 *
	 * @param	string	$name
	 * @return	Tag
	 */
	public function addValidAttribute($name)
	{
		if (! $this->isValidString($name)) {
			return $this;
		}

		/* already exists */
		if (in_array($name, $this->validAttrs)) {
			return $this;
		}

		$this->validAttrs[] = $name;
		return $this;
	}

	/**
	 * @param	array	$names	list of valid attributes
	 * @return	Tag
	 */
	public function addValidAttributes(array $names)
	{
		foreach ($names as $name) {
			$this->addValidAttribute($name);
		}

		return $this;
	}

	/**
	 * remove an attribute from the white list of valid attributes
	 *
	 * @param	string	$name	
	 * @return	Tag
	 */
	public function removeValidAttribute($name)
	{
		if (! $this->isValidString($name)) {
			return $this;
		}

		$isStrict = true;
		$key = array_search($name, $this->validAttrs, $isStrict); 
		if ($key) {
			unset($this->validAttrs[$key]);
		}

		return $this;
	}

	/**
	 * Checks to see if attribute validation is enabled. When it is then it
	 * checks that the attribute is a valid string and is also located in the 
	 * white list of valid attributes. When attribute validation is disabled
	 * it only checks if the attribute name is a valid string. When attributes
	 * have been disabled it always returns false.
	 *
	 * @param	string $name	
	 * @return	bool
	 */
	public function isValidAttribute($name)
	{
		if (! $this->isAttributesEnabled()) {
			return false;
		}

		/*
		 * attribute validation checks the attribute against a white list 
		 * of attributes allowed for a particular tag
		 */
		$isValidString = $this->isValidString($name);
		if (! $this->isAttributeValidation()) {
			return $isValidString;
		} 
 
		if ($isValidString && in_array($name, $this->validAttrs)) {
			return true;
		}

		return false;
	}

	/**
	 * @param	array	$attrs	list of attributes to add
	 * @return	Tag
	 */
	public function addAttributes(array $attrs)
	{
		/*
		 * we do this because addAttribute also checks and we can prevent
		 * several repeated checks with just one.
		 */
		if (! $this->isAttributesEnabled()) {
			return $this;
		}

		foreach ($attrs as $attr => $value) {
			$this->addAttribute($attr, $value);
		}
		return $this;
	}

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	Tag
	 */
	public function addAttribute($name, $value)
	{
		/*
		 * this handles conditions of disabled attributes, attribute 
		 * validation and string validation. We additionally check to make sure
		 * that value is a scalar data type because it will eventually be put
		 * in a string.
		 */
		if ($this->isValidAttribute($name) && is_scalar($value)) {
			$this->attrs[$name] = $value;
		}

		return $this;
	}

	/**
	 * @param	string	$name
	 * @return	Tag
	 */
	public function removeAttribute($name)
	{
		if ($this->attributeExists($name)) {
			unset($this->attrs[$name]);
		}

		return $this;
	}

	/**
	 * returns the value assigned to the attribute name given.
	 *
	 * @param	string	$name
	 * @return	string
	 */
	public function getAttribute($name, $default = null)
	{
		if ($this->attributeExists($name)) {
			return $this->attrs[$name];
		}

		return $default;
	}

	/**
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attrs;
	}

	/**
	 * @param	string	$name
	 * @return	bool
	 */
	public function attributeExists($name)
	{
		if (! $this->isValidString($name)) {
			return false;
		}

		if (array_key_exists($name, $this->attrs)) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string $str
	 * @return	boo
	 */
	protected function isValidString($str)
	{
		return is_string($str) && ! empty($str);
	}
}
