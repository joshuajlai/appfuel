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
	Appfuel\View\ViewTemplate,
	Appfuel\View\ViewCompositeTemplate,
	Appfuel\View\Html\Element\Tag as ElementTag,
	Appfuel\View\Html\Element\Base,
	Appfuel\View\Html\Element\Title,
	Appfuel\View\Html\Element\Link,
	Appfuel\View\Html\Element\Script,
	Appfuel\View\Html\Element\CssStyle,
	Appfuel\View\Html\Element\Meta\Charset,
	Appfuel\View\Html\Element\Meta\Tag as MetaTag,
	Appfuel\View\Html\Element\HtmlTagInterface,
	Appfuel\View\Compositor\FileCompositor,
	Appfuel\View\Compositor\FileCompositorInterface;


/**
 * Template used to generate generic html documents
 */
class HtmlDocTemplate extends ViewTemplate implements HtmlDocTemplateInterface
{
	/**
	 * Html title tag
	 * @var Element\Title
	 */
	protected $title = null;

	/**
	 * Attributes for the html tag
	 * @var array
	 */
	protected $htmlAttrs = array();

	/**
	 * Attributes for the head tag
	 * @var array
	 */
	protected $headAttrs = array();

	/**
	 * Attributes for the body tag
	 * @var array
	 */
	protected $bodyAttrs = array();

	/**
	 * Special meta tag to detemine the character set of the page. We only
	 * need a string because all the other attributes are fixed
	 * @var string
	 */
	protected $charset = null;

	/**
	 * @var string
	 */
	protected $base = null;

	/**
	 * @var array
	 */
	protected $meta = array();

	/**
	 * @var bool
	 */
	protected $isCss = true;
	
	/**
	 * @var array
	 */	
	protected $cssLinks = array();

	/**
	 * @var CssStyle
	 */
	protected $inlineCss = null;

	/**
	 * @var bool
	 */
	protected $isJs = true;

	/**
	 * Script tags add to the html head
	 * @var array
	 */
	protected $jsHeadScripts = array();

	/**
	 * Inline js for html head
	 * @var Script
	 */
	protected $jsHeadInline = null;

	/**
	 * Script tags added to the end of the html body
	 * @var array
	 */
	protected $jsBodyScripts = array();

	/**
	 * Inline js for the html body
	 * @var Script
	 */
	protected $jsBodyInline = null;

	/**
	 * We use a tag only for its ability to store content
	 * @var Tag
	 */
	protected $bodyContent = null;

	/**
	 * Sometimes it is necessary to have body content at the end, after
	 * the script tags. 
	 * @var Tag
	 */
	protected $finalBodyContent = null;

	/**
	 * Defaults to the appfuel template file 
	 *
	 * @param	string				$filePath	relative path to template file
	 * @param	PathFinderInterface $pathFinder	resolves the relative path
	 * @param	array				$data		data to be assigned
	 * @return	HtmlDocTemplate
	 */
	public function __construct($filePath = null,
								FileCompositorInterface $compositor = null,
								array $data = null)
	{
		if (null === $filePath) {
			$filePath = 'appfuel/html/htmldoc.phtml';
		}

		if (null === $compositor) {
			$compositor = new FileCompositor();
			$compositor->setFile($filePath);
		}

		parent::__construct($data, $compositor);
		$this->setTitleTag(new Title());
		$this->setCharset('UTF-8');
		$this->setCssStyleTag(new CssStyle());
		$this->setJsHeadInlineScriptTag(new Script());
		$this->setJsBodyInlineScriptTag(new Script());

		$this->bodyContent = new ElementTag();
		$this->finalBodyContent = new ElementTag();
	}

	/**
	 * @return	string
	 */
	public function build()
	{
		$this->buildTitle()
			 ->buildCharset()
			 ->buildBase()
			 ->buildAttributes()
			 ->buildCss()
			 ->buildJs()
			 ->buildContent();

		return parent::build();
	}

	/**
	 * @return	HtmlDocInterface
	 */
	public function buildTitle()
	{
		$this->assign('html-title', $this->getTitleTag());
		return $this;
	}

	/**
	 * @return	HtmlDocInterface
	 */
	public function buildCharset()
	{
		$encoding = $this->getCharset();
		if (! empty($encoding)) {
			$charset = new Charset($encoding);
			$this->assign('html-charset', $charset->build());
		}

		return $this;
	}

	/**
	 * @return	HtmlDocInterface
	 */
	public function buildBase()
	{
		$base = $this->getBaseTag();
		if ($base instanceof HtmlTagInterface) {
			$this->assign('html-base', $base->build());
		}
		
		return $this;
	}

	/**
	 * @return	HtmlDocInterface
	 */
	public function buildMeta()
	{
		$meta = $this->getMetaTags();
		if (! empty($meta)) {
			$this->assign('html-meta', $meta);
		}

		return $this;
	}

	/**
	 * @return	HtmlDocInterface
	 */
	public function buildCss()
	{
		$isCss = $this->isCssEnabled();
		$this->assign('is-css', $isCss);
		if ($isCss) {
			$links = $this->getLinkTags();
			if (! empty($links)) {
				$this->assign('links-css', $links);
			}

			$inlineCss = $this->getCssStyleTag();
			if ($inlineCss instanceof HtmlTagInterface) {
				$this->assign('inline-css', $inlineCss);
			}
		}
		return $this;
	}

	/**
	 * @return	HtmlDocInterface
	 */
	public function buildJs()
	{
		$isJs = $this->isJsEnabled();
		$this->assign('is-js', $isJs);
		if ($isJs) {
			$headScripts = $this->getJsHeadScriptTags();
			if (! empty($headScripts)) {
				$this->assign('scripts-js-head', $headScripts);
			}
			$headInline = $this->getJsHeadInlineScriptTag();
			if ($headInline instanceof HtmlTagInterface) {
				$this->assign('inline-js-head', $headInline);
			}

			$bodyScripts = $this->getJsBodyScriptsTags();
			if (! empty($bodyScripts)) {
				$this->assign('scripts-js-body', $bodyScripts);
			}

			$bodyInline = $this->getJsBodyInlineScriptTag();
			if ($bodyInline instanceof HtmlTagInterface) {
				$this->assign('inline-js-body', $bodyInline);
			}
		}

		return $this;
	}

	/**
	 * @return	HtmlDocInterface
	 */
	public function buildContent()
	{
		$contentTag = $this->getBodyContentTag();
		$this->assign('body-content', $contentTag->buildContent());
		
		$finalContentTag = $this->getFinalBodyContentTag();
		$this->assign('body-content-final', $finalContentTag->buildContent());

		return $this;
	}

	/**
	 * @return	HtmlDocInterface
	 */
	public function buildAttributes()
	{
		$attrs = $this->buildAttributeString($this->getHtmlAttributes());
		if (! empty($attrs)) {
			$this->assign('html-attr-string', $attrs);
		}

		$attrs = $this->buildAttributeString($this->getHeadAttributes());
		if (! empty($attrs)) {
			$this->assign('head-attr-string', $attrs);
		}

		$attrs = $this->buildAttributeString($this->getBodyAttributes());
		if (! empty($attrs)) {
			$this->assign('body-attr-string', $attrs);
		}
		return $this;
	}

	/**
	 * @return	HtmlTagInterface
	 */
	public function getTitleTag()
	{
		return $this->title;
	}

	/**
	 * @param	HtmlTagInterface	$tag
	 * @return	HtmlDocTemplate
	 */
	public function setTitleTag(HtmlTagInterface $tag)
	{
		if ('title' !== $tag->getTagName()) {
			throw new InvalidArgumentException('title must be a title tag');
		}
		$this->title = $tag;
		return $this;
	}

	/**
	 * @param	string	$title
	 * @param	string	$action
	 * @return	HtmlDocTemplate
	 */
	public function setTitle($title, $action = 'replace')
	{
		$this->getTitleTag()
			 ->addContent($title, $action);

		return $this;		
	}

	/**
	 * @param	string	$char
	 * @return	HtmlDocTemplate
	 */
	public function setTitleSeparator($char)
	{
		$this->getTitleTag()
			 ->setSeparator($char);

		return $this;
	}

	/**
	 * @return	string
	 */
	public function getCharset()
	{
		return $this->charset;
	}

	/**
	 * @param	string	$encoding
	 * @return	HtmlDocTemplate
	 */
	public function setCharset($encoding)
	{
		if (! is_string($encoding) || !($encoding = trim($encoding))) {
			$err = 'meta tag charset must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->charset =$encoding;
		return $this;
	}

	/**
	 * @return	HtmlTagInterface
	 */
	public function getBaseTag()
	{
		return $this->base;
	}

	/**
	 * @param	HtmlTagInterface $base
	 * @return	HtmlDocInterface
	 */
	public function setBaseTag(HtmlTagInterface $base)
	{
		if ('base' !== $base->getTagName()) {
			$err = 'Html tag set must be a base tag';
			throw new InvalidArgumentException($err);
		}

		$this->base = $base;
		return $this;
	}

	/**
	 * @param	string
	 * @param	string
	 * @return	HtmlDocInterface
	 */
	public function setBase($href = null, $target = null)
	{
		return $this->setBaseTag(new Base($href, $target));
	}

	/**
	 * @param	HtmlTagInterface $tag
	 * @return	HtmlDocTemplate
	 */
	public function addMetaTag(HtmlTagInterface $tag)
	{
		if ('meta' !== $tag->getTagName()) {
			$err = 'meta tag set must be a meta tag';
			throw new InvalidArgumentException($err);
		}

		$this->meta[] = $tag;
		return $this;
	}

	/**
	 * @param	string	$name
	 * @param	string	$content
	 * @return	HtmlDocTemplate
	 */
	public function addMeta($name = null, $content = null)
	{
		return $this->addMetaTag(new MetaTag($name, $content));
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
	public function isCssEnabled()
	{
		return $this->isCss;
	}

	/**
	 * @return	HtmlDocTemplate
	 */
	public function enableCss()
	{
		$this->isCss = true;
		return $this;
	}

	/**
	 * @return	HtmlDocTemplate
	 */
	public function disableCss()
	{
		$this->isCss = false;
		return $this;
	}

	/**
	 * @param	HtmlTagInterface $base
	 * @return	HtmlDocTemplate
	 */
	public function addLinkTag(HtmlTagInterface $link)
	{
		if ('link' !== $link->getTagName()) {
			$err = 'Html tag set must be a link tag';
			throw new InvalidArgumentException($err);
		}

		$this->cssLinks[] = $link;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getLinkTags()
	{
		return $this->cssLinks;
	}

	/**
	 * @param	array	$links	
	 * @return	HtmlDocTemplate
	 */
	public function loadLinkTags(array $links) 
	{
		foreach ($links as $link) {
			$this->addLinkTag($link);
		}

		return $this;
	}

	/**
	 * @param	string	$href
	 * @param	string	$rel
	 * @param	string	$type
	 * @return	HtmlDocTemplate
	 */
	public function addCssFile($href, $rel = null, $type = null)
	{
		return $this->addLinkTag(new Link($href, $rel, $type));
	}

	/**
	 * @param	array	$files
	 * @return	HtmlDocTemplate
	 */
	public function loadCssFiles(array $files)
	{
		foreach ($files as $file) {
			$href = '';
			$rel  = null;
			$type = null;
			if (is_string($file)) {
				$href = $file;
			}
			elseif (is_array($file)) {
				if (isset($file[0]) && is_string($file[0])) {
					$href = $file[0];
				}
				else {
					continue;
				}

				if (isset($file[1]) && is_string($file[1])) {
					$rel = $file[1];
				}
				if (isset($file[2]) && is_string($file[2])) {
					$type = $file[2];
				}
			}

			$this->addCssFile($href, $rel, $type);
		}

		return $this;
	}

	/**
	 * @return	CssStyle
	 */
	public function getCssStyleTag()
	{
		return $this->inlineCss;
	}

	/**
	 * @param	HtmlTagInterface $tag
	 * @return	HtmlDocTemplate
	 */
	public function setCssStyleTag(HtmlTagInterface $tag)
	{
		if ('style' !== $tag->getTagName()) {
			$err = 'Html tag set must be a style tag';
			throw new InvalidArgumentException($err);
		}

		$this->inlineCss = $tag;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isJsEnabled()
	{
		return $this->isJs;
	}

	/**
	 * @return	HtmlDocTemplate
	 */
	public function enableJs()
	{
		$this->isJs = true;
		return $this;
	}

	/**
	 * @return	HtmlDocTemplate
	 */
	public function disableJs()
	{
		$this->isJs = false;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getJsHeadScriptTags()
	{
		return array_values($this->jsHeadScripts);
	}
	
	/**
	 * The source is used to prevent the same script from being loaded twice
	 *
	 * @param	HtmlTagInterface $tag
	 * @return	HtmlDocInterface
	 */
	public function addJsHeadScriptTag(HtmlTagInterface $tag)
	{
		$src = $tag->getAttribute('src');
		if ('script' !== $tag->getTagName() || empty($src)) {
			$err  = 'js script must be an html script tag and have a non ';
			$err .= 'empty src attribute';
			throw new InvalidArgumentException($err);
		}

		if ($this->isJsHeadScript($src)) {
			return $this;
		}

		$this->jsHeadScripts[$src] = $tag;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	HtmlDocTemplate
	 */
	public function loadJsHeadScriptTags(array $list)
	{
		foreach ($list as $tag) {
			$this->addJsHeadScriptTag($tag);
		}

		return $this;
	}

	/**
	 * @param	string	src 
	 * @return	bool
	 */
	public function isJsHeadScript($src)
	{
		if (isset($this->jsHeadScripts[$src]) && 
			$this->jsHeadScripts[$src] instanceof HtmlTagInterface) {
			return true;
		}

		return false;
	}

	/**	
	 * @param	string	$src
	 * @return	HtmlDocTemplate
	 */
	public function addJsHeadFile($src)
	{
		if (! is_string($src) || empty($src)) {
			$err  = 'js src must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if ($this->isJsHeadScript($src)) {
			return $this;
		}

		$script = new Script($src);
		$this->jsHeadScripts[$src] = $script;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	HtmlDocTemplate
	 */
	public function loadJsHeadFiles(array $list)
	{
		foreach ($list as $src) {
			$this->addJsHeadFile($src);
		}

		return $this;
	}

	/**
	 * @return	Script
	 */
	public function getJsHeadInlineScriptTag()
	{
		return $this->jsHeadInline;
	}

	/**
	 * @param	HtmlTagInterface $tag
	 * @return	HtmlDocTemplate
	 */
	public function setJsHeadInlineScriptTag(HtmlTagInterface $tag)
	{
		if ('script' !== $tag->getTagName()) {
			$err  = 'js -(html head) inline script must be an html script tag';
			throw new InvalidArgumentException($err);
		}

		$src = $tag->getAttribute('src');
		if (is_string($src) && ! empty($src)) {
			$err = 'js -(html head) inline script can not have a source attr';
			throw new InvalidArgumentException($err);
		}

		$this->jsHeadInline = $tag;
		return $this;
	}

	/**
	 * @param	string	$text
	 * @return	HtmlDocTemplate
	 */
	public function addJsHeadInlineContent($jsContent)
	{
		$script = $this->getJsHeadInlineScriptTag();
		$script->addContent($jsContent);
		return $this;	
	}

	/**
	 * Retrieve only the contents of the script tag. Html Tag contents are
	 * stored as an array and then built into a string, isArray allows you 
	 * to get the contents as that array
	 *
	 * @param	bool	$isArray
	 * @return	array | string
	 */
	public function getJsHeadInlineContent($isArray = false)
	{
		$script = $this->getJsHeadInlineScriptTag();
		if (true === $isArray) {
			return $script->getContent();
		}

		return $script->buildContent();
	}

	/**
	 * @return	array
	 */
	public function getJsBodyScriptTags()
	{
		return array_values($this->jsBodyScripts);
	}
	
	/**
	 * The source is used to prevent the same script from being loaded twice
	 *
	 * @param	HtmlTagInterface $tag
	 * @return	HtmlDocInterface
	 */
	public function addJsBodyScriptTag(HtmlTagInterface $tag)
	{
		$src = $tag->getAttribute('src');
		if ('script' !== $tag->getTagName() || empty($src)) {
			$err  = 'js script must be an html script tag and have a non ';
			$err .= 'empty src attribute';
			throw new InvalidArgumentException($err);
		}

		if ($this->isJsBodyScript($src)) {
			return $this;
		}

		$this->jsBodyScripts[$src] = $tag;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	HtmlDocTemplate
	 */
	public function loadJsBodyScriptTags(array $list)
	{
		foreach ($list as $tag) {
			$this->addJsBodyScriptTag($tag);
		}

		return $this;
	}

	/**
	 * @param	string	src 
	 * @return	bool
	 */
	public function isJsBodyScript($src)
	{
		if (isset($this->jsBodyScripts[$src]) && 
			$this->jsBodyScripts[$src] instanceof HtmlTagInterface) {
			return true;
		}

		return false;
	}

	/**	
	 * @param	string	$src
	 * @return	HtmlDocTemplate
	 */
	public function addJsBodyFile($src)
	{
		if (! is_string($src) || empty($src)) {
			$err  = 'js src must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if ($this->isJsBodyScript($src)) {
			return $this;
		}

		$script = new Script($src);
		$this->jsBodyScripts[$src] = $script;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	HtmlDocTemplate
	 */
	public function loadJsBodyFiles(array $list)
	{
		foreach ($list as $src) {
			$this->addJsBodyFile($src);
		}

		return $this;
	}

	/**
	 * @return	Script
	 */
	public function getJsBodyInlineScriptTag()
	{
		return $this->jsBodyInline;
	}

	/**
	 * @param	HtmlTagInterface $tag
	 * @return	HtmlDocTemplate
	 */
	public function setJsBodyInlineScriptTag(HtmlTagInterface $tag)
	{
		if ('script' !== $tag->getTagName()) {
			$err  = 'js -(html head) inline script must be an html script tag';
			throw new InvalidArgumentException($err);
		}

		$src = $tag->getAttribute('src');
		if (is_string($src) && ! empty($src)) {
			$err = 'js -(html head) inline script can not have a source attr';
			throw new InvalidArgumentException($err);
		}

		$this->jsBodyInline = $tag;
		return $this;
	}

	/**
	 * @param	string	$text
	 * @return	HtmlDocTemplate
	 */
	public function addJsBodyInlineContent($jsContent)
	{
		$script = $this->getJsBodyInlineScriptTag();
		$script->addContent($jsContent);
		return $this;	
	}

	/**
	 * Retrieve only the contents of the script tag. Html Tag contents are
	 * stored as an array and then built into a string, isArray allows you 
	 * to get the contents as that array
	 *
	 * @param	bool	$isArray
	 * @return	array | string
	 */
	public function getJsBodyInlineContent($isArray = false)
	{
		$script = $this->getJsBodyInlineScriptTag();
		if (true === $isArray) {
			return $script->getContent();
		}

		return $script->buildContent();
	}

	/**
	 * @return	HtmlTagInterface
	 */
	public function getBodyContentTag()
	{
		return $this->bodyContent;
	}

	/**
	 * @param	string	$content
	 * @return	HtmlDocTemplate
	 */
	public function addBodyContent($data)
	{
		$tag = $this->getBodyContentTag();

        if (null !== $data &&
            ! is_string($data) && ! is_callable(array($data, '__toString'))) {
			$err  = 'body content must be string or an object that implements';
			$err .= '_toString';
			throw new InvalidArgumentException($err);
		}

		$tag->addContent((string)$data);
		return $this;
	}

	/**
	 * @param	bool	$isArray
	 * @return	string|array of content items 
	 */
	public function getBodyContent($isArray = false)
	{
		$tag = $this->getBodyContentTag();
		if (true === $isArray) {
			return $tag->getContent();
		}

		return $tag->buildContent();
	}

	/**
	 * @return	HtmlTagInterface
	 */
	public function getFinalBodyContentTag()
	{
		return $this->finalBodyContent;
	}

	/**
	 * @param	string	$content
	 * @return	HtmlDocTemplate
	 */
	public function addFinalBodyContent($data)
	{
		$tag = $this->getFinalBodyContentTag();

        if (null !== $data &&
            ! is_string($data) && ! is_callable(array($data, '__toString'))) {
			$err  = 'body content must be string or an object that implements';
			$err .= '_toString';
			throw new InvalidArgumentException($err);
		}

		$tag->addContent((string)$data);
		return $this;
	}

	/**
	 * @param	bool	$isArray
	 * @return	string|array of content items 
	 */
	public function getFinalBodyContent($isArray = false)
	{
		$tag = $this->getFinalBodyContentTag();
		if (true === $isArray) {
			return $tag->getContent();
		}

		return $tag->buildContent();
	}

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	HtmlDocTemplate
	 */
	public function addHtmlAttribute($name, $value = null)
	{
		if (! is_string($name) || ! ($name = trim($name))) {
			$err = 'html attribute name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (null !== $value && ! is_string($value)) {
			$err = 'html attribute value must be a string';
			throw new InvalidArgumentException($err);
		}
		$this->htmlAttrs[$name] = $value;
		return $this;
	}

	/**
	 * @param	array	$attrs
	 * @return	HtmlDocTemplate
	 */
	public function setHtmlAttributes(array $attrs)
	{
		foreach ($attrs as $name => $value) {
			$this->addHtmlAttribute($name, $value);
		}

		return $this;
	}

	/**
	 * @return	array
	 */
	public function getHtmlAttributes()
	{
		return $this->htmlAttrs;
	}

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	HtmlDocTemplate
	 */
	public function addHeadAttribute($name, $value = null)
	{
		if (! is_string($name) || ! ($name = trim($name))) {
			$err = 'head attribute name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (null !== $value && ! is_string($value)) {
			$err = 'head attribute value must be a string';
			throw new InvalidArgumentException($err);
		}
		$this->headAttrs[$name] = $value;
		return $this;
	}

	/**
	 * @param	array	$attrs
	 * @return	HtmlDocTemplate
	 */
	public function setHeadAttributes(array $attrs)
	{
		foreach ($attrs as $name => $value) {
			$this->addHeadAttribute($name, $value);
		}

		return $this;
	}

	/**
	 * @return	array
	 */
	public function getHeadAttributes()
	{
		return $this->headAttrs;
	}

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	HtmlDocTemplate
	 */
	public function addBodyAttribute($name, $value = null)
	{
		if (! is_string($name) || ! ($name = trim($name))) {
			$err = 'body attribute name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (null !== $value && ! is_string($value)) {
			$err = 'body attribute value must be a string';
			throw new InvalidArgumentException($err);
		}
		$this->bodyAttrs[$name] = $value;
		return $this;
	}

	/**
	 * @param	array	$attrs
	 * @return	HtmlDocTemplate
	 */
	public function setBodyAttributes(array $attrs)
	{
		foreach ($attrs as $name => $value) {
			$this->addBodyAttribute($name, $value);
		}

		return $this;
	}

	/**
	 * @return	array
	 */
	public function getBodyAttributes()
	{
		return $this->bodyAttrs;
	}

	/**
	 * Turn an associative array of name/value pairs into a string. When
	 * the value is null then the name will be printed without quotes this
	 * work for enemurated kewords and boolean attributes
	 *
	 * @param	array	$attrs
	 * @return	string
	 */
	public function buildAttributeString(array $attrs)
	{
        $result = '';
        foreach ($attrs as $attr => $value) {
			if (null === $value) {
				$result .= "$attr ";
			}
			else if (is_string($value)) {
				$result .= "$attr=\"$value\" ";
			}
        }

        return trim($result);
	}
}
