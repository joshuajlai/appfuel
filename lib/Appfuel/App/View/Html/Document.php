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
namespace Appfuel\App\View\Html;

use Appfuel\Framework\Exception,
	Appfuel\Framework\FileInterface,
	Appfuel\Framework\View\TemplateInterface,
	Appfuel\App\View\Template,
	Appfuel\View\Html\Element\Tag,
	Appfuel\View\Html\Element\Title,
	Appfuel\View\Html\Element\Base,
	Appfuel\View\Html\Element\Meta\Charset,
	Appfuel\View\Html\Element\Script,
	Appfuel\View\Html\Element\Style,
	Appfuel\View\Html\Element\Link,
	Appfuel\View\Html\Element\Meta\Tag as MetaTag;

/**
 * Html document template. Used to manage the html document. This template 
 * does not act on any content inside the body tag itself. 
 */
class Document extends Template implements TemplateInterface
{
	/**
	 * Title tag used in the head of the document
	 * @var Title
	 */
	protected $title = null;

	/**
	 * Specifies the default url and default target for all links on the page
	 * @var Base
	 */
	protected $base = null;

	/**
	 * Charset meta tag. Can only have one of these
	 * @var Charset
	 */
	protected $charset = null;

	/**
	 * A list of meta tags located in the head of the html document
	 * @var array
	 */
	protected $meta = array();

	/**
	 * List of link tags located in the head of the html document
	 * @var array
	 */
	protected $css = array();
	
	/**
	 * Flag used to determine if css is enabled
	 * @var bool
	 */
	protected $isCss = true;

	/**
	 * Inline css located in the head of the document
	 * @var Style
	 */
	protected $inlineCss = null;

	/**
	 * Flag used to determine if inline styles are enabled
	 * @var bool
	 */
	protected $isInlineCss = false;

	/**
	 * List of scripts located either in the head or end of the body
	 * @var array
	 */
	protected $js = array(
		'head' => array(),
		'body' => array(),
	);

	/**
	 * Flag used to determine if javascript is enabled. This would include
	 * inline js (head,body) and script tags (head, body)
	 * @var bool
	 */
	protected $isJs = true;

	/**
	 * Flag used to determine if either head or body inlineJs is enabled
	 * @var bool
	 */
	protected $isInlineJs = true;

	/**
	 * Document behavior located in the head of the document
	 * @var Script
	 */
	protected $inlineJs = array(
		'head' => null,
		'body' => null
	);

	/**
	 * Add the template file used to render the markup and assign 
	 * any name/value pairs into scope
	 *
	 * @param	array	scope data
	 * @return	Document
	 */
	public function __construct(array $data = array())
	{
		parent::__construct($data);
		$this->addFile('markup', 'doc/standard.phtml');
	}

	/**
	 * Build the template file into a string
	 * 
	 * @return string
	 */
	public function build()
	{
		$title = $this->getTitleTag();
		if (! $title instanceof Title) {
			$title = new Title('Appfuel Default Html Document');
		}
		$this->assign('html-head-title', $title->build());

		$base = $this->getBase();
		if ($base instanceof Base) {
			$this->assign('html-head-base', $base->build());
		}
	
		$charset = $this->getCharset();
		if ($charset instanceof Charset) {
			$this->assign('html-head-charset', $charset->build());
		}

		$metaTags = $this->getMeta();
		if (! empty($metaTags)) {
			$tags = array();
			foreach ($metaTags as $tag) {
				$tags[] = $tag->build();
			}
			$this->assign('html-head-meta', $tags);
		}

		if ($this->isCss()) {
			$cssTags = $this->getCssLinks();
			
			$tags = array();    
			foreach ($cssTags as $tag) {
				$tags[] = $tag->build();
			}
			$this->assign('html-head-links', $tags);

			if ($this->isInlineCss()) {
				$style = $this->getInlineCss();
				if ($style instanceof Style) {
					$this->assign('html-head-inline-style', $style->build());
				}
			}
		}

		if ($this->isJs()) {
			/* refactor this block into a single loop */
			$jsTags = $this->getJsScripts('head');
			if (! empty($jsTags)) {
				$tags = array();
				foreach ($tags as $tag) {
					$tags[] = $tag->build();
				}
				$this->assign('html-head-scripts', $tags);
			}

			$jsTags = $this->getJsScripts('body');
			if (! empty($jsTags)) {
				$tags = array();
				foreach ($tags as $tag) {
					$tags[] = $tag->build();
				}
				$this->assign('html-body-scripts', $tags);
			}

			if ($this->isInlineJs()) {
				$script = $this->getInlineJs('head');
				if ($script instanceof Script) {
					$this->assign('html-head-inline-script', $script->build());
				}

				$script = $this->getInlineJs('body');
				if ($script instanceof Script) {
					$this->assign('html-body-inline-script', $script->build());
				}
			}
		}

		/*
		 * The layout hold all the html content so there is not much
		 * sense building without it. This will produce an empty html
		 * document
		 */
		if (! $this->isLayout()) {
			return $this->buildFile('markup');
		}

		$layout  = $this->getLayout();
		$this->assign('layout-content', $layout->build()); 

		if ($this->fileExists('inline-css')) {
			$css = $this->buildFile('inline-css');
			$this->assign('inline-css', $css);
		}

		$inlineJs = '';
		if ($this->fileExists('inline-js')) {
		
		}
	}
	/**
	 * @return Title
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param	Title $tile
	 * @return	Doc
	 */
	public function setTitle(Title $tag)
	{
		$this->title = $tag;
		return $this;
	}

	/**
	 * @param	Base	$tag
	 * @return	Document
	 */
	public function setBase(Base $tag)
	{
		$this->base	= $tag;
		return $this;
	}

	/**
	 * @return	Base
	 */
	public function getBase()
	{
		return $this->base;
	}

	/**
	 * @param	Base	$tag
	 * @return	Document
	 */
	public function setCharset(Charset $tag)
	{
		$this->charset = $tag;
		return $this;
	}

	/**
	 * @return	Base
	 */
	public function getCharset()
	{
		return $this->charset;
	}

	/**
	 * Adds all meta tags accept the charset. Only one charset is allowed
	 * and it is set through setCharset.
	 *
	 * @param	MetaTag $tag
	 * @return	Document
	 */
	public function addMeta(MetaTag $tag)
	{
		if ($tag->attributeExists('charset')) {
			return $this;
		}

		$this->meta[] = $tag;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getMeta()
	{
		return $this->meta;
	}

	/**
	 * @param	Link	$tag
	 * @return	Document
	 */
	public function addCssLink(Link $tag)
	{
		$this->css[] = $tag;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getCssLinks()
	{
		return $this->css;
	}

	/**
	 * @return bool
	 */
	public function enableCss()
	{
		$this->isCss = true;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function disableCss()
	{
		$this->isCss = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isCss()
	{
		return $this->isCss;
	}

	/**
	 * @param	Style	$tag
	 * @return	Document
	 */
	public function setInlineCss(Style $tag)
	{
		$this->inlineCss = $tag;
		return $this;
	}

	/**
	 * @return Style
	 */
	public function getInlineCss()
	{
		return $this->inlineCss;
	}

	/**
	 * @return bool
	 */
	public function enableInlineCss()
	{
		$this->isInlineCss = true;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function disableInlineCss()
	{
		$this->isInlineCss = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isInlineCss()
	{
		return $this->isInlineCss;
	}

	/**
	 * @param	Script	$tag
	 * @return	Document
	 */
	public function addJsScript(Script $tag, $location = 'body')
	{
		if (! in_array($location, array('body', 'head'))) {
			return $this;
		}

		$this->js[$location][] = $tag;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getJsScripts($location = 'body')
	{		
		if (! in_array($location, array('body', 'head'))) {
			return array();
		}
		return $this->js[$location];
	}

	/**
	 * @return bool
	 */
	public function enableJs()
	{
		$this->isJs = true;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function disableJs()
	{
		$this->isJs = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isJs()
	{
		return $this->isJs;
	}

	/**
	 * @param	Script	$tag
	 * @return	Document
	 */
	public function setInlineJs(Script $tag, $location = 'body')
	{
		if (! in_array($location, array('body', 'head'))) {
			return $this;
		}

		$this->inlineJs[$location] = $tag;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getInlineJs($location)
	{		
		if (! in_array($location, array('body', 'head'))) {
			return null;
		}
		return $this->inlineJs[$location];
	}


	/**
	 * @return bool
	 */
	public function enableInlineJs()
	{
		$this->isInlineJs = true;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function disableInlineJs()
	{
		$this->isInlineJs = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isInlineJs()
	{
		return $this->isInlineJs;
	}
}
