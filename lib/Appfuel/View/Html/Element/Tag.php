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

use InvalidArgumentException;

/**
 * This class looks to model the html5 tag. It has attribute validation which
 * allows you to indicate which attributes are valid for a given tag. Also 
 * has added all the html5 global attributes sa valid attributes so they do 
 * not need to be declared with every class extending this. Currently this
 * class does not support the validation of the attribute values. The interface
 * always you to add and remove attributes and content. It also allows you to
 * enable or disable the closing tag as some tags do not require the closing
 * tag. There are seperate methods for building content, the attribute string
 * and the tag itself.
 */
class Tag implements HtmlTagInterface
{
	/**
	 * Used to separate content
	 * @var string
	 */
	protected $separator = ' ';

	/**
	 * White list of accepted attributes. This list is populated with html5
	 * global attributes.
	 *
	 * @var array
	 */
	protected $validAttrs = array();

	/**
	 * Used to hold html tag attributes
	 * @var array
	 */
	protected $attrs = array();

	/**
	 * Used to hold the contents of the tag
	 * @var array
	 */ 
	protected $content = array();

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
	 * Name of the html tag. This name will be used to generate the actual
	 * tag string ex) <table> </table> the tag name is table
	 */
	protected $tagName = null;

	/**
	 * Used to determine if a full closing tag is needed
	 * @var bool
	 */
	protected $isClosingTag = true;

	/**
	 * @return string
	 */
	public function getTagName()
	{
		return $this->tagName;
	}

	/**
	 * @param	string	name of the tag
	 * @return	Tag
	 */
	public function setTagName($name)
	{
		if (! $this->isValidString($name)) {
			throw new InvalidArgumentException(
				"Invalid name for tag must be a string"
			);
		}
		$this->tagName = $name;
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function isClosingTag()	
	{
		return $this->isClosingTag;
	}

	/**
	 * @return Tag
	 */
	public function enableClosingTag()
	{
		$this->isClosingTag = true;
		return $this;
	}

	/**
	 * @return Tag
	 */
	public function disableClosingTag()
	{
		$this->isClosingTag = false;
		return $this;
	}

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
	 * @return array
	 */
	public function getAttributeWhiteList()
	{
		return array_merge(GlobalAttributes::get(), $this->validAttrs);;
	}

	/**
	 * remove all valid attributes from the list
	 *
	 * @return	Tag
	 */
	public function clearAttributeWhiteList()
	{
		$this->validAttrs = array();
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
		if ($key !== false) {
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

		if (GlobalAttributes::exists($name)) {
			return true;
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
	 * @return int
	 */
	public function attributeCount()
	{
		return count($this->attrs);
	}

	/**
	 * Add content to the tag
	 * 
	 * @param	mixed	$data	
	 * @param	string	$action		what to do with the content
	 * @return	Tag
	 */
    public function addContent($data, $action = 'append')
    {
        /*
         * make sure the data is an array
         */
        if (! is_array($data)) {
            $data = array($data);
        }

        /*
         * content data structure does not exits. This could be caused
         * by the first use in the constructor or a concrete class blew
         * it away. Either way we recover by adding data to it and we are done
         */
        if (empty($this->content)) {
            $this->content = $data;
            return $this;
        }

        switch ($action) {
            case 'append':
                $this->content = array_merge($this->content, $data);
                break;
            case 'prepend':
                $this->content = array_merge($data, $this->content);
                break;
            case 'replace':
                $this->content = $data;
                break;
			default :
				$this->content = $data;
        }

        return $this;
    }

	/**
	 * @return array
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @return int
	 */
	public function contentCount()
	{
		return count($this->content);
	}

	/**
	 * Build the content by concatenating each item in the content array,
	 * use the separator between each item.
	 *
	 * @return string
	 */
	public function buildContent()
	{
        $content  = $this->getContent();
        $sep      = $this->getSeparator();
        $str = '';
        foreach ($content as $index => $item) {
            if (is_scalar($item)) {
                $str .= $sep . $item;
            } else if (is_array($item)) {
                $str .= implode($sep, $item);
            } else if (is_object($item) && method_exists($item, '__toString')) {
                $str .= $sep . $item->__toString();
            }
        }

        return trim($str, $sep);
	}

	/**
	 * Used to build a string of attr=value sets for the tag when 
	 * attributes are disabled it returns an empty string
	 *
	 * @return string
	 */
	public function buildAttributes()
	{
		if (! $this->isAttributesEnabled()) {
			return '';
		}

		$attrs = $this->getAttributes();
		$result = '';
		foreach ($attrs as $attr => $value) {
			$result .= "$attr=\"$value\" ";
		}

		return trim($result);
	}

	/**
	 * @return string
	 */
	public function build()
	{
		$tagName = $this->getTagName();
		
		/* an html element with no tag name can not be rendered to anything
		 * useful
		 */
		if (empty($tagName)) {
			return '';
		}
		
		$isClosingTag = $this->isClosingTag();
		$attrCount    = $this->attributeCount();

		/* an html element that need no closing tag must have some attributes
		 * otherwise it servers no purpose
		 */
		if (! $isClosingTag && $attrCount === 0) {
			return '';
		}

		$tag = "<{$tagName}";
		
		/* add the attributes for this element */
		if ($this->isAttributesEnabled()) {
			$tag .= ' ' . $this->buildAttributes();
			$tag  = trim($tag);
		}
		
		/* the last parent of the element is dependent on wether it needs
		 * a closing tag or not. When no closing tag is required no content
		 * is used for that tag
		 */
		if ($isClosingTag) {
			$content = $this->buildContent();
			$tag .= ">{$content}</{$tagName}>";
		} 
		else {
			$tag .= '/>';
		}
		
		return $tag;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->build();
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
