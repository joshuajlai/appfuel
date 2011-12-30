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

use LogicException,
	InvalidArgumentException;

/**
 * Handles all tags associated with the html head.
 */
class HeadTag extends GenericTag
{
	/**
	 * Html head title tag
	 * @var GenericTagInterface
	 */
	protected $title = null;

	/**
	 * Html base tag 
	 * @var GenericTagInterface
	 */
	protected $base = null;

	/**
	 * List of meta tags
	 * @var	array
	 */
	protected $meta = array();

	/**
	 * List of link tags
	 * @var array
	 */
	protected $links = array();

	/**
	 * Inline Style tag
	 * @var	StyleTag
	 */
	protected $style = null;

	/**
	 * List of ScriptTags
	 * @var array
	 */
	protected $scripts = array();

	/**
	 * Single script for inline javascript
	 * @var ScriptTag
	 */
	protected $inlineScript = null;

	/**
	 * @var array
	 */
	protected $contentOrder = array(
		'Title', 
		'Base', 
		'Meta', 
		'Links', 
		'Style', 
		'Scripts', 
		'InlineScript' 
	);

	/**
	 * @param	string	$data	content for the title
	 * @return	Title
	 */
	public function __construct($data = null, $sep = PHP_EOL)
	{
		$content = new TagContent($data, $sep);
		parent::__construct('head', $content);
		$this->setTitle(new TitleTag());
	}

	/**
	 * Fix the tag to only be a head tag
	 *
	 * @param	string	$name
	 * @return	HeadTag
	 */
	public function setTagName($name)
	{
		if ('head' !== $name) {
			$err = 'this tag can only be a head tag';
			throw new LogicException($err);
		}

		return parent::setTagName($name);
	}

	/**
	 * @return	GenericTagInterface
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function setTitle(GenericTagInterface $tag)
	{
		if ('title' !== $tag->getTagName()) {
			$err = 'must have a tag name of -(title)';
			throw new InvalidArgumentException($err);
		}
		
		$this->title = $tag;
		return $this;
	}

	/**
	 * @param	string	$text
	 * @param	string	$action	
	 * @return	HeadTag
	 */
	public function setTitleText($text, $action = 'append')
	{
		$this->getTitle()
			 ->addContent($text, $action);

		return $this;
	}

	/**
	 * @param	string	$char
	 * @return	HeadTag
	 */
	public function setTitleSeparator($char)
	{
		$this->getTitle()
			 ->setContentSeparator($char);

		return $this;	
	}

	/**
	 * @return	bool	
	 */
	public function isTitle()
	{
		return $this->title instanceof GenericTagInterface;
	}

	/**
	 * @return	GenericTagInterface
	 */
	public function getBase()
	{
		return $this->base;
	}

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function setBase(GenericTagInterface $tag)
	{
		if ('base' !== $tag->getTagName()) {
			$err = 'must have a tag name of -(base)';
			throw new InvalidArgumentException($err);
		}
		
		$this->base = $tag;
		return $this;
	}

	/**
	 * @return	bool	
	 */
	public function isBase()
	{
		return $this->base instanceof GenericTagInterface;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function addMeta(GenericTagInterface $tag)
	{
		if ('meta' !== $tag->getTagName()) {
			$err = 'must have a tag name of -(meta)';
			throw new InvalidArgumentException($err);
		}

		$this->meta[] = $tag;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getMeta()
	{
		return $this->meta;
	}

	/**
	 * @return	bool
	 */
	public function isMeta()
	{
		return count($this->meta) > 0;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function addLink(GenericTagInterface $tag)
	{
		if ('link' !== $tag->getTagName()) {
			$err = 'must have a tag name of -(link)';
			throw new InvalidArgumentException($err);
		}

		$this->links[] = $tag;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getLinks()
	{
		return $this->links;
	}

	/**
	 * @return	bool
	 */
	public function isLinks()
	{
		return count($this->links) > 0;
	}

	/**
	 * @return	GenericTagInterface
	 */
	public function getStyle()
	{
		return $this->style;
	}

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function setStyle(GenericTagInterface $tag)
	{
		if ('style' !== $tag->getTagName()) {
			$err = 'must have a tag name of -(title)';
			throw new InvalidArgumentException($err);
		}
		
		$this->style = $tag;
		return $this;
	}

	/**
	 * @return	bool	
	 */
	public function isStyle()
	{
		return $this->style instanceof GenericTagInterface;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function addScript(GenericTagInterface $tag)
	{
		if ('script' !== $tag->getTagName()) {
			$err = 'must have a tag name of -(script)';
			throw new InvalidArgumentException($err);
		}

		if (! $tag->isAttribute('src')) {
			$err = 'only script tags that have src attributes are allowed';
			throw new InvalidArgumentException($err);
		}

		$this->scripts[] = $tag;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getScripts()
	{
		return $this->scripts;
	}

	/**
	 * @return	bool
	 */
	public function isScripts()
	{
		return count($this->scripts) > 0;
	}

	/**
	 * @return	GenericTagInterface
	 */
	public function getInlineScript()
	{
		return $this->inlineScript;
	}

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HeadTag
	 */
	public function setInlineScript(GenericTagInterface $tag)
	{
		if ('script' !== $tag->getTagName()) {
			$err = 'must have a tag name of -(title)';
			throw new InvalidArgumentException($err);
		}

		if ($tag->isAttribute('src')) {
			$err = 'src attribute is not allowed for an inline script';
			throw new InvalidArgumentException($err);
		}	

		$this->inlineScript = $tag;
		return $this;
	}

	/**
	 * @return	bool	
	 */
	public function isInlineScript()
	{
		return $this->inlineScript instanceof GenericTagInterface;
	}

	/**
	 * @return	array
	 */
	public function getContentOrder()
	{
		return $this->contentOrder;
	}

	public function setContentOrder(array $items)
	{
		$this->contentOrder = $items;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function build()
	{
		$content = $this->getTagContent();
		$attrs   = $this->getTagAttributes();

		$order = $this->getContentOrder();
		foreach ($order as $item) {
			$getter = 'get' . ucfirst($item);
			if (! method_exists($this, $getter)) {
				continue;
			}
			$tag = $this->$getter();
			if (is_array($tag) && ! empty($tag)) {
				foreach ($tag as $listTag) {
					$content->add($listTag);
				}
			}
			else if ($tag instanceof GenericTagInterface) {
				$content->add($tag);
			}
		}

		return $this->buildTag($content, $attrs);
	}
}
