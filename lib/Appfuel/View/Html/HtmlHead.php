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
namespace Appfuel\View\Html;

use InvalidArgumentException,
	Appfuel\View\Html\Tag\HeadTagInterface,
	Appfuel\View\Html\Tag\GenericTagInterface,
	Appfuel\View\Html\Tag\HtmlTagFactoryInterface;

/**
 */
class HtmlHead implements HtmlHeadInterface
{
	/**
	 * @var HtmlTagFactoryInterface
	 */
	protected $factory = null;

	/**
	 * @var HeadTagInterface
	 */
	protected $head = null;

	/**
	 * @var GenericTagInterface
	 */
	protected $base = null;

	/**
	 * List of meta tags
	 */
	protected $meta = array();

	/**
	 * List of javascript files to be loaded in the head tag
	 * @var array
	 */
	protected $scripts = array();

	/**
	 * List of link tags to be loaded in the head tag
	 * @var array
	 */
	protected $cssTags = array();

	/**
	 * Style tag that will appear in the head
	 * @var GenericTagInterface
	 */
	protected $inlineStyle = null;

	/**	
	 * Inline javascript that will appear in the head
	 * @var GenericTagInterface
	 */
	protected $inlineScript = null;

	/**
	 * Flag used to determine if javascript is enabled for the body
	 * @var bool
	 */
	protected $isJs = true;

	/**
	 * Flag used to determine if css should be resources should be built
	 * @var bool
	 */
	protected $isCss = true;

	/**
	 * @param	GenericTagInterface  $body	 html body tag
	 * @param	GenericTagInterface  $inline inline js script tag
	 * @return	HtmlBody
	 */
	public function __construct(HtmlTagFactoryInterface $factory)
	{
		$this->setHeadTag($factory->createHeadTag());
		$this->setInlineStyleTag($factory->createStyleTag());
		$this->setInlineScriptTag($factory->createScriptTag());

		$this->factory = $factory;
	}

	/**
	 * @return	HtmlTagInterface
	 */
	public function getHeadTag()
	{
		return $this->head;
	}

	/**
	 * @param	HtmlDocInterface $doc
	 * @return	HtmlPage
	 */
	public function setHeadTag(HeadTagInterface $tag)
	{
		if ('head' !== $tag->getTagName()) {
			$err = 'head tag must have a tag name of -(head)';
			throw new InvalidArgumentException($err);
		}

		$this->head = $tag;
		return $this;
	}

	/**	
	 * @param	string	$name
	 * @return	string	$value
	 */
	public function addAttribute($name, $value = null)
	{
		$this->getHeadTag()
			 ->addAttribute($name, $value);

		return $this;
	}

	/**
	 * @param	string	$name
	 * @param	string	$default
	 * @return	mixed
	 */
	public function getAttribute($name, $default = null)
	{
		return $this->getHeadTag()
					->getAttribute($name, $default);
	}

	/**
	 * @param	string	$name
	 * @return	bool
	 */
	public function isAttribute($name)
	{
		return $this->getHeadTag()
					->isAttribute($name);
	}

	/**
	 * @param	string	$text	
	 * @param	string	$action
	 * @return	HtmlHead
	 */
	public function setTitle($text, $action = 'append')
	{
		$this->getHeadTag()
			 ->getTitle()
			 ->addContent($text, $action);

		return $this;
	}

	/**
	 * @param	string	$char	
	 * @return	HtmlHead
	 */
	public function setTitleSeparator($char)
	{
		$this->getHeadTag()
			 ->getTitle()
			 ->setContentSeparator($char);

		return $this;
	}

	/**
	 * @param	string	$char	
	 * @return	HtmlHead
	 */
	public function getTitleSeparator()
	{
		return $this->getHeadTag()
					->getTitle()
					->getContentSeparator();
	}

	/**
	 * @return	GenericTagInterface
	 */
	public function getBaseTag()
	{
		return $this->base;
	}

	/**
	 * @param	GenericTagInterface	$tag
	 * @return	HtmlHead
	 */
	public function setBaseTag(GenericTagInterface $tag)
	{
		if ('base' !== $tag->getTagName()) {
			$err = 'tag name must be base';
			throw new InvalidArgumentException($err);
		}

		$this->base = $tag;
		return $this;
	}

	/**
	 * @param	string	$href
	 * @param	string	$target
	 * @return	HtmlHead
	 */
	public function setBase($href = null, $target = null)
	{
		$tag = $this->getTagFactory()
					->createBaseTag($href, $target);

		return $this->setBaseTag($tag);
	}

	/**
	 * @param	mixed	$name
	 * @param	string	$content	
	 * @param	string	$equiv
	 * @param	string	$charset
	 * @return	HtmlHead
	 */
	public function addMeta($name = null, 
							$content = null, 
							$equiv = null,
							$charset = null)
	{
		$tag = $this->getTagFactory()
					->createMetaTag($name, $content, $equiv, $charset);

		return $this->addMetaTag($tag);
	}

	/**	
	 * @param	GenericTagInterface $tag
	 * @return	HtmlHead
	 */
	public function addMetaTag(GenericTagInterface $tag)
	{
		if ('meta' !== $tag->getTagName()) {
			$err = 'tag must have a tagName of -(meta)';
			throw new InvalidArgumentException($err);
		}

		$this->meta[] = $tag;
		return $this;
	}

	/**	
	 * @return	array
	 */
	public function getMetaTags()
	{
		return $this->meta;
	}
 
	/**
	 * @return	bool
	 */
	public function isJs()
	{
		return $this->isJs;
	}

	/**
	 * @return	HtmlBody
	 */
	public function enableJs()
	{
		$this->isJs = true;
		return $this;
	}

	/**
	 * @return	HtmlBody
	 */
	public function disableJs()
	{
		$this->isJs = false;
		return $this;
	}
	
	/**
	 * @return	bool
	 */
	public function isCss()
	{
		return $this->isCss;
	}

	/**
	 * @return	HtmlHead
	 */
	public function enableCss()
	{
		$this->isCss = true;
		return $this;
	}

	/**
	 * @return	HtmlHead
	 */
	public function disableCss()
	{
		$this->isCss = false;
		return $this;
	}

	/**
	 * @return	ScriptTag
	 */
	public function getInlineStyleTag()
	{
		return $this->inlineStyle;
	}

	/**
	 * @param	GenericTagInterface	 $tag
	 * @return	HtmlBody
	 */
	public function setInlineStyleTag(GenericTagInterface $tag)
	{
		if ('style' !== $tag->getTagName()) {
			$err = 'this must be a script tag';
			throw new InvalidArgumentException($err);
		}

		$this->inlineStyle = $tag;
		return $this;
	}

	/**
	 * @param	mixed	string | object supporting __toString
	 * @return	HtmlBody
	 */
	public function addInlineStyleContent($data)
	{
		$this->getInlineStyleTag()
			 ->addContent($data);

		return $this;
	}

	/**
	 * @param	int	$index 
	 * @return	string | array
	 */
	public function getInlineStyleContent($index = null)
	{
		return $this->getInlineStyleTag()
					->getContent($index);
	}

	/**
	 * @return	string
	 */
	public function getInlineStyleContentString()
	{
		return $this->getInlineStyleTag()
					->getContentString();
	}

	/**
	 * @param	mixed	$src
	 * @return	HtmlBody
	 */
	public function addCssTag($href, $rel = null, $type = null)
	{
		if (is_string($href) && ! empty($href)) {
			$tag = $this->getTagFactory()
						   ->createLinkTag($href, $rel, $type);
		}
		else if (($href instanceof GenericTagInterface) && 
				 ('link' === $href->getTagName() || 
				 'style' === $href->getTagName())) {
			$tag = $href;
		}
		else {
			$err  = 'must be a string or an Appfuel\View\Hmtml\GenericTag';
			$err .= 'Interface with a tag name of link or style';
			throw new InvalidArgumentException($err);
		}

		$this->cssTags[] = $tag;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getCssTags()
	{
		return $this->cssTags;
	}

	/**
	 * @return	int
	 */
	public function getCssTagCount()
	{
		return count($this->cssTags);
	}

	/**
	 * @return	ScriptTag
	 */
	public function getInlineScriptTag()
	{
		return $this->inlineScript;
	}

	/**
	 * @param	GenericTagInterface	 $tag
	 * @return	HtmlBody
	 */
	public function setInlineScriptTag(GenericTagInterface $tag)
	{
		if ('script' !== $tag->getTagName()) {
			$err = 'this must be a script tag';
			throw new InvalidArgumentException($err);
		}

		if ($tag->isAttribute('src')) {
			$err = 'script tag can not have a src attribute';
			throw new InvalidArgumentException($err);
		}

		$this->inlineScript = $tag;
		return $this;
	}

	/**
	 * @param	mixed	string | object supporting __toString
	 * @return	HtmlBody
	 */
	public function addInlineScriptContent($data)
	{
		$this->getInlineScriptTag()
			 ->addContent($data);

		return $this;
	}

	/**
	 * @param	int	$index 
	 * @return	string | array
	 */
	public function getInlineScriptContent($index = null)
	{
		return $this->getInlineScriptTag()
					->getContent($index);
	}

	/**
	 * @return	string
	 */
	public function getInlineScriptContentString()
	{
		return $this->getInlineScriptTag()
					->getContentString();
	}

	/**
	 * @param	mixed	$src
	 * @return	HtmlBody
	 */
	public function addScript($src)
	{
		if (is_string($src) && ! empty($src)) {
			$script = $this->getTagFactory()
						   ->createScriptTag($src);
		}
		else if (($src instanceof GenericTagInterface) && 
				 'script' === $src->getTagName()) {
			$script = $src;
		}
		else {
			$err  = 'must be a string or an Appfuel\View\Hmtml\GenericTag';
			$err .= 'Interface with a tag name of script';
			throw new InvalidArgumentException($err);
		}

		$this->scripts[] = $script;
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
	 * @return	int
	 */
	public function getScriptCount()
	{
		return count($this->scripts);
	}

	/**
	 * @return	string
	 */
	public function build()
	{
		$head = $this->configure();
		return $head->build();
	}

	public function configure(HeadTagInterface $head = null)
	{
		if (null === $head) {
			$head = $this->getHeadTag();
		}

		$base = $this->getBaseTag();
		if ($base) {
			$head->setBase($base);
		}

		$metaList = $this->getMetaTags();
		foreach ($metaList as $metaTag) {
			$head->addMeta($metaTag);
		}

		if ($this->isCss()) {
			/* add the inline style as the last css tag */
			$this->addCssTag($this->getInlineStyleTag());
			
			$cssList  = $this->getCssTags();
			foreach ($cssList as $cssTag) {
				$head->addCssTag($cssTag);
			}
		}

		if ($this->isJs()) {
			/* add the inline script as the last script tag */
			$this->addScript($this->getInlineScriptTag());
			$scripts = $this->getScripts();
			foreach ($scripts as $scriptTag) {
				$head->addScript($scriptTag);
			}
		}

		return $head;
	}

	/**
	 * @return	HtmlTagFactoryInterface
	 */
	protected function getTagFactory()
	{
		return $this->factory;	
	}
}
