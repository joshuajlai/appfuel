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
namespace Appfuel\View\Html\Tag;

use RunTimeException,
	InvalidArgumentException;

/**
 * TagAttributes holds a list of attributes for a given html tag.
 */
class TagAttributes implements TagAttributesInterface
{
    /**
	 * Html5 global attributes considered valid for all html tags
     * @var array
     */
    static protected $global = array(
        'accessKey',
        'class',
        'contextmenu',
        'dir',
        'draggable',
        'dropzone',
        'hidden',
        'id',
        'lang',
        'spellcheck',
        'style',
        'tabindex',
        'title'
    );

	/**
	 * Whitelist of accepted attributes
	 * @var array
	 */
	protected $valid = array();

	/**
	 * List of attributes stored as name => value pairs
	 * @var array
	 */
	protected $attrs = array();

	/**
	 * Flag used to determine if a tag should validate
	 * the attributes that we assigned to it
	 * @var bool
	 */
	protected $isValidation = true;

	/**
	 * @param	array	$attrs	white list of valid attributes
	 * @return	TagAttributes
	 */
	public function __construct(array $attrs = null)
	{
		if (null !== $attrs) {
			$this->loadWhiteList($attrs);
		}
	}

	/**
	 * @return bool
	 */
	public function isValidation()
	{
		return $this->isValidation;
	}

	/**
	 * @return TagAttributes
	 */
	public function enableValidation()
	{
		$this->isValidation = true;
		return $this;
	}

	/**
	 * @return TagAttributes
	 */
	public function disableValidation()
	{
		$this->isValidation = false;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getWhiteList($isGlobal = true)
	{
		if (false === $isGlobal) {
			return $this->valid;
		}

		return array_merge(self::$global, $this->valid);
	}

	/**
	 * remove all valid attributes from the list
	 *
	 * @return	Tag
	 */
	public function clearWhiteList()
	{
		$this->valid = array();
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getGlobalAttributes()
	{
		return self::$global;
	}

	/**
	 * Add a valid attribute to the white list
	 *
	 * @param	string	$attr
	 * @return	Tag
	 */
	public function addToWhiteList($attr)
	{
		if (! is_string($attr) || ! ($attr = trim($attr))) {
			$err = 'white listed attribute must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (! in_array($attr, self::$global) && 
			! in_array($attr, $this->valid)) {
			$this->valid[] = $attr;
		}

		return $this;
	}

	/**
	 * @param	array	$names	list of valid attributes
	 * @return	Tag
	 */
	public function loadWhiteList(array $names)
	{
		foreach ($names as $name) {
			$this->addToWhiteList($name);
		}

		return $this;
	}

	/**
	 * @param	string $name	
	 * @return	bool
	 */
	public function isValid($name)
	{
		if (! $this->isValidString($name)) {
			return false;
		}

		if (! $this->isValidation()) {
			return true;
		}

		if (in_array($name, self::$global, true) ||
			in_array($name, $this->valid, true)) {
			return true;
		}
	 
		return false;
	}

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	Tag
	 */
	public function add($name, $value = null)
	{
		if (empty($name) || ! ($name = trim($name))) {
			$err = 'attribute name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if ($value !== null &&	! is_string($value)) {
			$err = 'attribute value must be null or a string';
			throw new InvalidArgumentException($err);
		}

		if (! $this->isValid($name)) {
			$err = "attribute has failed validation -($name)";
			throw new RunTimeException($err); 
		}

		$this->attrs[$name] = $value;
		return $this;
	}

	/**
	 * @param	array	$attrs	list of attributes to add
	 * @return	Tag
	 */
	public function load(array $attrs)
	{
		foreach ($attrs as $attr => $value) {
			$this->add($attr, $value);
		}
		return $this;
	}

	/**
	 * returns the value assigned to the attribute name given.
	 *
	 * @param	string	$name
	 * @return	string
	 */
	public function get($name, $default = null)
	{
		if ($this->exists($name)) {
			return $this->attrs[$name];
		}

		return $default;
	}

	/**
	 * @return array
	 */
	public function getAll()
	{
		return $this->attrs;
	}

	/**
	 * Clear out any current attributes
	 * 
	 * @return	TagAttributes
	 */
	public function clear()
	{
		$this->attrs = array();
		return $this;
	}

	/**
	 * @param	string	$name
	 * @return	bool
	 */
	public function exists($name)
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
	 * @return int
	 */
	public function count()
	{
		return count($this->attrs);
	}

	/**
	 * Used to build a string of attr=value sets for the tag when 
	 * attributes are disabled it returns an empty string
	 *
	 * @return string
	 */
	public function build()
	{
		$result = '';
		$attrs  = $this->getAll();
		foreach ($attrs as $attr => $value) {
			if (null === $value) {
				$result .= "$attr ";
			}
			else {
				$result .= "$attr=\"$value\" ";
			}
		}

		return trim($result);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->build();
	}

	protected function isValidString($str)
	{
		if (! is_string($str) || empty($str)) {
			return false;
		}

		return true;
	}
}
