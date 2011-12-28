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
	Appfuel\View\FileViewTemplate,
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
class HtmlDocTemplate extends FileViewTemplate implements HtmlDocInterface
{
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
	 * @param	string				$file		relative path to template file
	 * @param	PathFinderInterface $pathFinder	resolves the relative path
	 * @return	HtmlDocTemplate
	 */
	public function __construct($file = null,
								PathFinderInterface $pathFinder = null)
	{
		if (null === $file) {
			$file = 'appfuel/html/tpl/doc/htmldoc.phtml';
		}
		parent::__construct($file, $pathFinder);
		
		$this->useDocType('html5');
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
		$this->buildAttributes()
			 ->buildContent();

		return parent::build();
	}

	/**
	 * @return	HtmlDocInterface
	 */
	public function buildJs()
	{
		$isJs = $this->isJsEnabled();
		if ($isJs) {
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
		return $this->get('html-title', null);
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
		$this->assign('html-title', $tag);
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
		return $this->get('html-charset');
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
			
		$this->assign('html-charset', new Charset($encoding));
		return $this;
	}

	/**
	 * @return	HtmlTagInterface
	 */
	public function getBaseTag()
	{
		return $this->get('html-base');
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

		$this->assign('html-base', $base);
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

		return $this->assignIntoArray('html-meta', $tag);
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
		return $this->get('html-meta', array());
	}

	/**
	 * @return	bool
	 */
	public function isCssEnabled()
	{
		$isCss = $this->get('is-css', true);
		return ($isCss === false) ? false : true;
	}

	/**
	 * @return	HtmlDocTemplate
	 */
	public function enableCss()
	{
		$this->assign('is-css', true);
		return $this;
	}

	/**
	 * @return	HtmlDocTemplate
	 */
	public function disableCss()
	{
		$this->assign('is-css', false);
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

		$this->assignIntoArray('links-css', $link);
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getLinkTags()
	{
		return $this->get('links-css', array());
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

		return $this->assign('inline-css', $tag);
	}

	/**
	 * @return	bool
	 */
	public function isJsEnabled()
	{
		$isJs = $this->get('is-js', true);
		return ($isJs === false) ? false : true;
	}

	/**
	 * @return	HtmlDocTemplate
	 */
	public function enableJs()
	{
		return $this->assign('is-js', true);
	}

	/**
	 * @return	HtmlDocTemplate
	 */
	public function disableJs()
	{
		return $this->assign('is-js', false);
	}

	/**
	 * @return	array
	 */
	public function getJsHeadScriptTags()
	{
		$list = $this->get('scripts-js-head', array());
		return (is_array($list)) ? array_values($list) : array();
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

		return $this->assignIntoAssocArray('scripts-js-head', $src, $tag);
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
		$list = $this->get('scripts-js-head', array());
		if (is_array($list) && 
			isset($list[$src]) &&
			$list[$src] instanceof HtmlTagInterface) {
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

		return $this->addJsHeadScriptTag(new Script($src));
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
		return $this->get('inline-js-head', null);
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

		return $this->assign('inline-js-head', $tag);
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
		$list = $this->get('scripts-js-body', array());
		return (is_array($list)) ? array_values($list) : array();
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

		return $this->assignIntoAssocArray('scripts-js-body', $src, $tag);
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
		$list = $this->get('scripts-js-body', array());
		if (is_array($list) && 
			isset($list[$src]) &&
			$list[$src] instanceof HtmlTagInterface) {
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

		return $this->addJsBodyScriptTag(new Script($src));
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
		return $this->get('inline-js-body', null);
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

		return $this->assign('inline-js-body', $tag);
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

	/**
	 * @return	string | null when not set
	 */
	public function getDocType()
	{
		return $this->get('doctype', null);
	}

	/**
	 * @param	string	$docType
	 * @return	HtmlDocTemplate
	 */
	public function setDocType($docType)
	{
		if (! is_string($docType) || empty($docType)) {
			$err = 'doctype must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		return $this->assign('doctype', $docType);
	}

	/**
	 * @param	string	$type
	 * @return	null
	 */
	public function useDocType($type)
	{
		if (! is_string($type) || empty($type)) {
			$err = 'doctype must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		$docType = $this->getDocTypeString($type);
		if (false === $docType) {
			$err = "doctype key -($type) has not been mapped";
			throw new InvalidArgumentException($err);
		}

		return $this->setDocType($docType);
	}

	/**
	 * @param	string	$type
	 * @return	string
	 */
	public function getDocTypeString($type)
	{
		switch ($type) {
			case 'html5': 
				$text = '<!DOCTYPE HTML>';
				break;

			case 'html401-strict':
				$text = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">';
				break;
			case 'html401-transitional':
				$text = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">';
				break;
			case 'html401-frameset':
				$text = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">';
				break;
			case 'xhtml10-strict':
				$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
				break;
			case 'xhtml10-transitional':
				$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			case 'xhtml10-frameset':
				$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
				break;
			case 'xhtml11':
				$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
				break;
			case 'xhtml11-basic':
				$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
    "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">';
				break;
			case 'mathml20':
				$text = '<!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN"	
	"http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">';
				break;
			case 'mathml101':
				$text = '<!DOCTYPE math SYSTEM 
	"http://www.w3.org/Math/DTD/mathml1/mathml.dtd">';
				break;
			case 'xhtml+mathml+svg':
				$text = '<!DOCTYPE html PUBLIC
    "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN"
    "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">';
				break;
			case 'xhtml-host+mathml+svg':
				$text = '<!DOCTYPE html PUBLIC
    "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN"
    "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">';
				break;
			case 'xhtml+mathml+svg-host':
				$text = '<!DOCTYPE svg:svg PUBLIC
    "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN"
    "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">';
				break;
			case 'svg11-full':
				$text = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
	"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">';
				break;
			case 'svg10':
				$text = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN"
	"http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">';
				break;
			case 'svg11-basic':
				$text = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Basic//EN"
	"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11-basic.dtd">';
				break;
			case 'svg11-tiny':
				$text = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Tiny//EN"
	"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11-tiny.dtd">';
				break;
			default: $text = false;

		}
			
		return $text;
	}
}
