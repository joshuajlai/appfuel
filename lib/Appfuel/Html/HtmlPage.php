<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Html;

use DomainException,
	InvalidArgumentException,
	Appfuel\View\FileTemplate,
	Appfuel\View\ViewInterface,
	Appfuel\Html\Tag\HtmlTagFactory,
	Appfuel\Html\Tag\HeadTagInterface,
	Appfuel\Html\Tag\HtmlTagInterface,
	Appfuel\Html\Tag\GenericTagInterface,
	Appfuel\Html\Tag\HtmlTagFactoryInterface;

/**
 * The html page is a template object with an interface used to add various 
 * elements to it. Its main concern is with the html tag, the head tag, and
 * the body tag. You can add attributes, script tags, link tags, title, meta
 * etc.. It also has one special feature that any assigments don't go into the
 * the document template but the view template
 */
class HtmlPage extends FileTemplate implements HtmlPageInterface
{
	/**
	 * @var TagFactoryInterface
	 */
	protected $tagFactory = null;

	/**
	 * @var HtmlTagInterface
	 */
	protected $htmlTag = null;
	
	/**
	 * Single style tag that will hold on the inline sytle content for the 
	 * page. The framework can just keep adding content blocks to this tag
	 * instead of adding new style tags
	 * @var	GenericTagInterface
	 */
	protected $inlineStyle = null;

	/**
	 * List of script tags to be added after the markup
	 * @var string
	 */
	protected $scripts = array();

	/**
	 * Single script tag that will hold on the inline js content for the 
	 * page. The framework can just keep adding content blocks to this tag
	 * instead of adding new script tags. This tag will be the last to
	 * appear at the end of the body
	 * @var	GenericTagInterface
	 */
	protected $inlineScript = null;

	/**
	 * Flag used to determine if javascript is enabled both in the head and
	 * body
	 * @var bool
	 */
	protected $isJs = true;
	
	/**	
	 * Flag used to detemine if css is enabled. Only applies in the head
	 * @var bool
	 */
	protected $isCss = true;

	/**
	 * Content used in the body tag
	 * @var string
	 */
	protected $view = '';

	/**
	 * @param	string|ViewInterface $view	
	 * @param	string				 $htmlDocFile	path to phtml file
	 * @param	HtmlTagFactory		 $factory
	 * @return	HtmlPage
	 */
	public function __construct($file=null, HtmlTagFactoryInterface $fact=null)
	{
		if (null === $file) {
			$file = 'appfuel/web/htmldoc/doc.phtml';
		}
		parent::__construct($file, false);

		if (null === $fact) {
			$fact = new HtmlTagFactory();
		}

		$this->setHtmlTag($fact->createHtmlTag());
		$this->setInlineStyleTag($fact->createStyleTag());
		$this->setInlineScriptTag($fact->createScriptTag());
		$this->setTagFactory($fact);
	}

	/**
	 * @return	ViewInterface
	 */
	public function getView()
	{
		return $this->getTemplate('content');
	}

	/**
	 * @return	HtmlPage
	 */
	public function setView(ViewInterface $view)
	{
		$this->addTemplate('content', $view);
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isView()
	{
		return $this->isTemplate('content');
	}

	/**
	 * @param	string	$name
	 * @param	string	$default
	 * @return	HtmlPage
	 */
	public function setViewPkg($name, $default = null)
	{
		$view = $this->createFileTemplate($name, true, $default);
		$this->addTemplate('content', $view);
		return $this;
	}

	/**
	 * @return	
	 */
	public function getTagFactory()
	{
		return $this->tagFactory;
	}

	/**
	 * @return	HtmlTagInterface
	 */
	public function getHtmlTag()
	{
		return $this->htmlTag;
	}

	/**
	 * @param	HtmlDocInterface $doc
	 * @return	HtmlPage
	 */
	public function setHtmlTag(HtmlTagInterface $tag)
	{
		if ('html' !== $tag->getTagName()) {
			$err = 'html tag must have a tag name of -(html)';
			throw new InvalidArgumentException($err);
		}

		$this->htmlTag = $tag;
		return $this;
	}

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	HtmlPage
	 */
	public function addHtmlAttribute($name, $value = null)
	{
		$this->getHtmlTag()
			 ->addAttribute($name, $value);

		return $this;
	}

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	HtmlPage
	 */
	public function addHeadAttribute($name, $value = null)
	{
		$this->getHtmlTag()
			 ->getHead()
			 ->addAttribute($name, $value);

		return $this;
	}

	/**
	 * @param	string	$text 
	 * @param	string	$action
	 * @return	HtmlPage
	 */
	public function setHeadTitle($text, $action = 'append')
	{
		$this->getHtmlTag()
			 ->getHead()
			 ->getTitle()
			 ->addContent($text, $action);

		return $this;
	}

	/**
	 * @param	string	$href
	 * @param	string	$target
	 * @return	HtmlPage
	 */
	public function setHeadBase($href = null, $target = null)
	{
		$tag = $this->getTagFactory()
					->createBaseTag($href, $target);

		$this->getHtmlTag()
			 ->getHead()
			 ->setBase($tag);

		return $this;
	}

	/**
     * @param   string  $name
     * @param   string  $content
     * @param   string  $httpEquiv
     * @param   string  $charset
	 * @return	HtmlPage
	 */
	public function addHeadMeta($name = null,
								$content = null,
								$httpEquiv = null,
								$charset = null)
	{
		$tag = $this->getTagFactory()
					->createMetaTag($name, $content, $httpEquiv, $charset);

		return $this->addHeadMetaTag($tag);
	}

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HtmlPage
	 */
	public function addHeadMetaTag(GenericTagInterface $tag)
	{
		$this->getHtmlTag()
			 ->getHead()
			 ->addMeta($tag);

		return $this;
	}

	/**
     * @param   string  $href   url or file path to resource  
     * @param   string  $rel    relationship between current doc and link
     * @param   string  $type   mime type
	 * @return	HtmlPage
	 */
	public function addCssLink($src, $rel = null, $type = null)
	{
		$tag = $this->getTagFactory()
					->createLinkTag($src, $rel, $type);
			
		$this->getHtmlTag()
			 ->getHead()
			 ->addCssTag($tag);

		return $this;
	}

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HtmlPage
	 */
	public function addCssLinkTag(GenericTagInterface $tag)
	{
		if ('link' !== $tag->getTagName()) {
			$err = 'can not add css tag: object must be link tag';
            throw new InvalidArgumentException($err);
        }

		$this->getHtmlTag()
			 ->getHead()
			 ->addCssTag($tag);

		return $this;
	}

	/**
	 * @param	string	$content	
	 * @param	string	$type
	 * @param	string	$sep
	 * @return	HtmlPage
	 */
	public function addCssStyle($content, $type = null, $sep = null)
	{
		$tag = $this->getTagFactory()
					->createStyleTag($content, $type, $sep);

		$this->getHtmlTag()
			 ->getHead()
			 ->addCssTag($tag);

		return $this;
	}

	/**
	 * @return	GenericTagInterface
	 */
	public function getInlineStyleTag()
	{
		return $this->inlineStyle;
	}

	/**
	 * @param	GenericeTagInterface $tag
	 * @return	HtmlPage
	 */
	public function setInlineStyleTag(GenericTagInterface $tag)
	{
		if ('style' !== $tag->getTagName()) {
			$err = 'inline style must have a tag name of -(style)';
			throw new InvalidArgumentException($err);
		}

		$this->inlineStyle = $tag;
		return $this;
	}

	/**
	 * @param	string	$content
	 * @return	HtmlPage
	 */
	public function addToInlineStyle($content)
	{
		$this->getInlineStyleTag()
			 ->addContent($content);

		return $this;
	}

	/**
	 * @param	int	$index	index of content block in the style tag
	 * @return	mixed
	 */
	public function getInlineStyleContent($index = null)
	{
		return $this->getInlineStyleTag()
					->getContent($index);
	}

	/**
	 * @return	bool
	 */
	public function isCss()
	{
		return $this->isCss;
	}

	/**
	 * @return	HtmlPage
	 */
	public function enableCss()
	{
		$this->isCss = true;
		return $this;
	}

	/**
	 * @return	HtmlPage
	 */
	public function disableCss()
	{
		$this->isCss = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isJs()
	{
		return $this->isJs;
	}

	/**
	 * @return	HtmlPage
	 */
	public function enableJs()
	{
		$this->isJs = true;
		return $this;
	}

	/**
	 * @return	HtmlPage
	 */
	public function disableJs()
	{
		$this->isJs = false;
		return $this;
	}

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	HtmlPage
	 */
	public function addBodyAttribute($name, $value = null)
	{
		$this->getHtmlTag()
			 ->getBody()
			 ->addAttribute($name, $value);

		return $this;
	}

	/**
	 * @param	string|TagInterface	$src
	 * @return	HtmlPage
	 */
	public function addScript($src, $type = null)
	{
		$tag = $this->getTagFactory()
					->createScriptTag($src, null, null, $type);

		return $this->addScriptTag($tag);
	}

	/**
	 * @param	GenericTagInterface	$src
	 * @return	HtmlPage
	 */
	public function addScriptTag(GenericTagInterface $tag)
	{
        if ('script' !== $tag->getTagName()) {
            $err = 'must have a tag name of -(script)';
            throw new InvalidArgumentException($err);
        }

        $this->scripts[] = $tag;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getScriptTags()
	{
		return $this->scripts;
	}

	/**
	 * @return	GenericTagInterface
	 */
	public function getInlineScriptTag()
	{
		return $this->inlineScript;
	}
	
	/**
	 * @param	GenericTagInterface	$src
	 * @return	HtmlPage
	 */
	public function setInlineScriptTag(GenericTagInterface $tag)
	{
        if ('script' !== $tag->getTagName()) {
            $err = 'the inline script must have a tag name of -(script)';
            throw new InvalidArgumentException($err);
        }

        if ($tag->isAttribute('src')) {
            $err = 'inline script tag can not have a src attribute';
            throw new InvalidArgumentException($err);
        }

        $this->inlineScript = $tag;
	}

	/**
	 * @param	string	$content	
	 * @param	string	$action
	 * @return	HtmlPage
	 */
	public function addToInlineScript($content, $action = 'append')
	{
		$this->getInlineScriptTag()
			 ->addContent($content, $action);

		return $this;
	}

	/**
	 * @param	int	$index
	 * @return	mixed
	 */
	public function getInlineScriptContent($index = null)
	{
		return $this->getInlineScriptTag()
					->getContent($index);
	}
	
	/**
	 * @return	string
	 */
	public function build()
	{
		$html = $this->getHtmlTag();
		$head = $html->getHead();
		$body = $html->getBody();
		
		$data = array(
			'html-attrs' => $html->getAttributeString(),
			'head-attrs' => $head->getAttributeString(),
			'head-title' => $head->getTitle(),
			'body-attrs' => $body->getAttributeString(),
		);
		
		if ($head->isBase()) {
			$data['head-base'] = $head->getBase();
		}
		
		if ($head->isMeta()) {
			$data['head-meta'] = $head->getMeta();
		}
		
		if ($this->isCss()) {
			/* the inline style tag is the last css tag in the head */
			$head->addCssTag($this->getInlineStyleTag());			
			$data['head-css'] = $head->getCssTags();
		}

		$data['body-markup'] = $this->buildView($body);

		if ($this->isJs()) {
			/* make sure the inline script tag is the last tag */
			$this->addScriptTag($this->getInlineScriptTag());	
			$data['body-js'] = $this->getScriptTags();
		}

		/*
		 * All assignments in the html page are actually for the view content
		 * template. We need to clear those out since the view has them by now,
		 * and replace them with the data needed for the html doc
		 */	
		$this->setAssignments($data);
		return parent::build();
	}

	/**
	 * Move all assignments into the view. Build the view into a string, note
	 * that when the view has dynamically generated javascript a.k.a (pjs) 
	 * view composition (build) will return an array of two strings, content,
	 * and js in that order. Content is assigned to the body tag and js is 
	 * assigned as content to the inline script tag. We finally turn the
	 * body tag content into a string and return it.
	 *
	 * @throws	DomainException
	 * @param	HtmlTagInterface	$body
	 * @return	string
	 */
	protected function buildView(GenericTagInterface $body)
	{	
		if (! $this->isView()) {
			return '';
		}
	
		if ('body' !== $body->getTagName()) {
			$err = 'build view will only accept an html body tag';
			throw new DomainException($err);
		}
		
		$view = $this->getView();
		$view->load($this->getAll());
		$result  = $view->build();
			
		$content = '';
		$initJs  = null;
		if (is_array($result)) {
			$content = current($result);
			$initJs  = next($result);
			if ($this->isJs() && is_string($initJs)) {
				$this->addToInlineScript($initJs, 'append');				
			}
		}
		else if (is_string($result)) {
			$content = $result;
		}
			
		$body->addContent($content, 'prepend');
		return $body->getContentString();
	}

	/**
	 * @param	HtmlTagFactoryInterface	$factory
	 * @return	null
	 */
	protected function setTagFactory(HtmlTagFactoryInterface $factory)
	{
		$this->tagFactory = $factory;
	}

	/**
	 * @param	string	$name
	 * @param	bool	$isPkg
	 * @param	string	$vendor
	 * @return	FileTemplate
	 */
	protected function createFileTemplate($name, $isPkg = false, $vendor = null)
	{
		return new FileTemplate($name, $isPkg, $vendor);
	}	
}
