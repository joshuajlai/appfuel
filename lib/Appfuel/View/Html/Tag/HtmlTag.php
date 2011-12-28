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
class HtmlTag implements HtmlTagInterface
{
	/**
	 * Used to separate content
	 * @var string
	 */
	protected $separator = ' ';

	/**
	 * Used to hold html tag attributes
	 * @var TagAttributesInterface
	 */
	protected $attrs = null;

	/**
	 * Used to hold the contents of the tag
	 * @var array
	 */ 
	protected $content = array();

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
	 * @param	array	$attrs	list of attributes to add
	 * @return	Tag
	 */
	public function loadAttributes(array $attrs)
	{
	}

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	Tag
	 */
	public function addAttribute($name, $value)
	{
	}

	/**
	 * returns the value assigned to the attribute name given.
	 *
	 * @param	string	$name
	 * @return	string
	 */
	public function getAttribute($name, $default = null)
	{
	}

	public function isAttribute($name)
	{
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
