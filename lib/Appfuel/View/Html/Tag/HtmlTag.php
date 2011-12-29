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
 */
class HtmlTag implements HtmlTagInterface
{
	/**
	 * Used to hold html tag attributes
	 * @var TagAttributesInterface
	 */
	protected $attrs = null;

	/**
	 * Used to hold the contents of the tag
	 * @var TagContentInterface
	 */ 
	protected $content = null;

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
	 * @param	TagContentInterface $content 
	 * @param	TagAttributesInterface $attrs
	 * @return	HtmlTag
	 */
	public function __construct($tagName, 
								TagContentInterface $content = null,
								TagAttributesInterface $attrs = null)
	{
		$this->setTagName($tagName);
		if (null === $content) {
			$content = new TagContent();
		}
		$this->content = $content;

		if (null === $attrs) {
			$attrs = new TagAttributes();
		}
		$this->attrs = $attrs;
	}

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
		if (! is_string($name) || ! ($name = trim($name))) {
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
	 * @param	array	$attrs	list of attributes to add
	 * @return	Tag
	 */
	public function loadAttributes(array $attrs)
	{
		$this->getTagAttributes()
			 ->load($attrs);
		return $this;
	}

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	Tag
	 */
	public function addAttribute($name, $value = null)
	{
		$this->getTagAttributes()
			 ->add($name, $value);

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
		return $this->getTagAttributes()
					->get($name, $default);
	}

	/**
	 * @param	string	$name
	 * @return	bool
	 */
	public function isAttribute($name)
	{
		return $this->getTagAttributes()
					->exists($name);
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
		$this->getTagContent()
			 ->add($data, $action);

        return $this;
    }

	/**
	 * @return array
	 */
	public function getContent($index = null)
	{
		return $this->getTagContent()
					->get($index);
	}

	public function clearContent($index = null)
	{
		return $this->getTagContent()
					->clear($index);
	}

	/**
	 * Build the content by concatenating each item in the content array,
	 * use the separator between each item.
	 *
	 * @return string
	 */
	public function getContentString()
	{
		return $this->getTagContent()
					->build();

	}

	/**
	 * @return string
	 */
	public function getAttributeString()
	{
		return $this->getTagAttributes()
					->build();
	}

	/**
	 * @return string
	 */
	public function build()
	{
		return $this->buildTag(
				$this->getTagContent(), 
				$this->getTagAttributes()
		);
	}

	/**
	 * @param	string|TagContentInterface	$content
	 * @param	string|TagAttributesInterface $attrs
	 * @return	string
	 */
	public function buildTag($content, $attrs = '')
	{
		if (! (is_string($attrs) || 
			  ($attrs instanceof TagAttributesInterface))) {
			$err  = 'attributes must be a string or an object that implements ';
			$err .= 'Appfuel\View\Html\Tag\TagAttributesInterface';
			throw new InvalidArgumentException($err);
		}

		if (! (is_string($content) ||
			  ($content instanceof TagContentInterface))) {
			$err  = 'content must be a string of an object that implements ';
			$err .= 'Appfuel\View\Html\Tag\TagContentInterface';
			throw new InvalidArgumentException($err);
		}

		$tagName = $this->getTagName();
		/* an html element with no tag name can not be rendered to anything
		 * useful
		 */
		if (empty($tagName)) {
			return '';
		}
		$isClosingTag = $this->isClosingTag();
		$tag = "<{$tagName}";

		$isAttrs = (is_string($attrs) && ! empty($attrs)) ||
				  ($attrs instanceof TagAttributesInterface && 
				   $attrs->count() > 0);
			
			
		/* an html element that need no closing tag must have some attributes
		 * otherwise it servers no purpose
		 */
		if (! $isClosingTag && ! $isAttrs) {
			return '';
		}

		if ($isAttrs) {
			$tag .= " $attrs";
		}
	
		/*
		 * content is only used for tags that close
		 */	
		if ($isClosingTag) {
			$tag .= ">$content</{$tagName}>";
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
	 * @return	TagAttributesInterface
	 */
	protected function getTagAttributes()
	{
		return $this->attrs;
	}

	/**
	 * @return	TagContentInterface
	 */
	protected function getTagContent()
	{
		return $this->content;
	}
}
