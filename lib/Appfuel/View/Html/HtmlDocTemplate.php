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
	Appfuel\View\Html\Element\Base,
	Appfuel\View\Html\Element\Title,
	Appfuel\View\Html\Element\Link,
	Appfuel\View\Html\Element\Style,
	Appfuel\View\Html\Element\Meta\Charset,
	Appfuel\View\Html\Element\Meta\Tag as MetaTag,
	Appfuel\View\Html\Element\HtmlTagInterface,
	Appfuel\View\Html\Compositor\HtmlDocCompositor,
	Appfuel\View\Html\Compositor\HtmlDocCompositorInterface;


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
	 * @var bool
	 */
	protected $isJs = true;

	/**
	 * Defaults to the appfuel template file 
	 *
	 * @param	string				$filePath	relative path to template file
	 * @param	PathFinderInterface $pathFinder	resolves the relative path
	 * @param	array				$data		data to be assigned
	 * @return	HtmlDocTemplate
	 */
	public function __construct($filePath = null,
								HtmlDocCompositorInterface $compositor = null,
								array $data = null)
	{
		if (null === $filePath) {
			$filePath = 'htmldoc.phtml';
		}

		if (null === $compositor) {
			$compositor = new HtmlDocCompositor($filePath);
			$compositor->setRelativeRootPath('ui/appfuel/html')
					   ->setFile($filePath);
		}

		parent::__construct($data, $compositor);
		$this->setTitleTag(new Title());
		$this->setCharset('UTF-8');
	}

	/**
	 * @return	string
	 */
	public function build()
	{
		$this->assign('title', $this->getTitleTag());
		
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

		$encoding = $this->getCharset();
		if (! empty($encoding)) {
			$charset = new Charset($encoding);
			$this->assign('html-charset', $charset->build());
		}

		$base = $this->getBaseTag();
		if ($base instanceof HtmlTagInterface) {
			$this->assign('html-base', $base->build());
		}
		
		$meta = $this->getMetaTags();
		if (! empty($meta)) {
			$this->assign('html-meta', $meta);
		}

		$isCss = $this->isCssEnabled();
		$this->assign('is-css', $this->isCssEnabled());
		if ($isCss) {
			$links = $this->getLinkTags();
			if (! empty($links)) {
				$this->assign('links-css', $links);
			}

			$inlineCss = $this->getStyleTag();
			if ($inlineCss instanceof HtmlTagInterface) {
				$this->assign('inline-css', $inlineCss);
			}
		}

		return parent::build();
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
	public function setLinkTags(array $links) 
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
	public function setCssFiles(array $files)
	{
		foreach ($files as $file) {
			$href = null;
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
					throw new InvalidArgumentException(
						'first item -(href) in array is required'
					);
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
