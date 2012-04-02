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

use InvalidArgumentException,
	Appfuel\View\ViewData,
	Appfuel\View\ViewDataInterface,
	Appfuel\Html\Tag\HtmlTagFactory,
	Appfuel\Html\Tag\HeadTagInterface,
	Appfuel\Html\Tag\HtmlTagInterface,
	Appfuel\Html\Tag\GenericTagInterface,
	Appfuel\Html\Tag\HtmlTagFactoryInterface;

/**
 * Template used to generate generic html documents
 */
class HtmlPage extends ViewData implements HtmlPageInterface
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
	public function __construct(HtmlTagFactoryInterface $factory = null)
	{
		if (null === $factory) {
			$factory = new HtmlTagFactory();
		}

		$this->setHtmlTag($factory->createHtmlTag());
		$this->setInlineStyleTag($factory->createStyleTag());
		$this->setInlineScriptTag($factory->createScriptTag());
		$this->setTagFactory($factory);
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
	public function getView()
	{
		return $this->view;
	}

	/**
	 * @param	string	$data
	 * @return	HtmlPage
	 */
	public function setView($data)
	{
		if (! is_string($data) || 
			! (is_object($data) && is_callable(array($data, '__toString')))) {
			$err  = 'view must be a string or an object that implments ';
			$err .= '__toString';
			throw new InvalidArgumentException($err);
		}

		$this->view =(string) $data;
		return $this;
	}


	/**
	 * @return	string
	 */
	public function build()
	{
		$ns   = 'doc';
		$html = $this->getHtmlTag();
		$head = $html->getHead();
		$body = $html->getBody();
		$this->assign('html-attrs', $html->getAttributeString(), $ns);
		$this->assign('head-attrs', $head->getAttributeString(), $ns);
		$this->assign('head-title', $head->getTitle(), $ns);
		if ($head->isBase()) {
			$this->assign('head-base', $head->getBase(), $ns);
		}
		
		if ($head->isMeta()) {
			$this->assign('head-meta', $head->getMeta(), $ns);
		}
		
		if ($this->isCss()) {
			/* the inline style tag is the last css tag in the head */
			$head->addCssTag($this->getInlineStyleTag());			
			$this->assign('head-css', $head->getCssTags(), $ns);
		}

		$this->assign('body-attrs', $body->getAttributeString());

		$body->addContent((string)$this->getView(), 'prepend');
		$template->assign('body-markup', $body->getContentString());

		if ($this->isJs()) {
			
			/* add as the last script tag for the page */
			$this->addScriptTag($this->getInlineScriptTag());			
			$template->assign('body-js', $this->getScriptTags());
		}
		return $template->build();
	}

	/**
	 * @param	HtmlTagFactoryInterface	$factory
	 * @return	null
	 */
	protected function setTagFactory(HtmlTagFactoryInterface $factory)
	{
		$this->tagFactory = $factory;
	}
}
