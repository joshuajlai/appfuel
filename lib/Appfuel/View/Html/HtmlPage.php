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
	Appfuel\View\ViewInterface,
	Appfuel\View\FileViewTemplate,
	Appfuel\View\Html\Tag\HtmlTag,
	Appfuel\View\Html\Tag\ScriptTag,
	Appfuel\View\Html\Tag\HtmlTagFactory,
	Appfuel\View\Html\Tag\HtmlTagInterface,
	Appfuel\View\Html\Tag\GenericTagInterface,
	Appfuel\View\Html\Tag\HtmlTagFactoryInterface;

/**
 * Template used to generate generic html documents
 */
class HtmlPage extends ViewTemplate implements HtmlPageInterface
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
	 * @var HtmlHead
	 */
	protected $htmlHead = null;

	/**
	 * @var HtmlBody
	 */
	protected $htmlBody = null;

	/**
	 * Key used to add and get the view template
	 * @var string
	 */
	protected $contentKey = 'content';

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
	 * @param	string|ViewInterface $view	
	 * @param	HtmlTagInterface	 $htmlTag
	 * @param	HtmlHeadInterface	 $htmlHead
	 * @param	HtmlBodyInterface	 $htmlBody
	 * @param	HtmlTagFactory		 $factory
	 * @return	HtmlPage
	 */
	public function __construct($view = null,
								$htmlDocFile = null,
								HtmlTagInterface $htmlTag = null,
								HtmlHeadInterface $htmlHead = null,
								HtmlBodyInterface $htmlBody = null,
								HtmlTagFactoryInterface $factory = null)
	{
		if (null !== $view) {
			$this->setView($view);
		}
		
		if (null === $factory) {
			$factory = new HtmlTagFactory();
		}

		if (null === $htmlTag) {
			$htmlTag = $factory->createHtmlTag();
		}
		$this->setHtmlTag($htmlTag);

		if (null === $htmlHead) {
			$htmlHead = new HtmlHead($factory);
		}
		$this->setHtmlHead($htmlHead);

		if (null === $htmlBody) {
			$htmlBody = new HtmlBody($factory);
		}
		$this->setHtmlBody($htmlBody);
		$this->setTagFactory($factory);

		if (null === $htmlDocFile) {
			$htmlDocFile = 'appfuel/html/tpl/doc/htmldoc.phtml';
		}
		$htmlDoc = new FileViewTemplate($htmlDocFile);
		$this->addTemplate('htmldoc', $htmlDoc);
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
	 * @return	HtmlHeadInterface
	 */
	public function getHtmlHead()
	{
		return $this->htmlHead;
	}
	
	/**
	 * @param	HtmlHeadInterface $head
	 * @return	HtmlPage
	 */
	public function setHtmlHead(HtmlHeadInterface $head)
	{
		$this->htmlHead = $head;
		return $this;
	}

	/**
	 * @return	HtmlBodyInterface
	 */
	public function getHtmlBody()
	{
		return $this->htmlBody;
	}

	/**
	 * @param	HtmlBodyInterface $head
	 * @return	HtmlPage
	 */
	public function setHtmlBody(HtmlBodyInterface $body)
	{
		$this->htmlBody = $body;
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
		$this->getHtmlHead()
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
		$this->getHtmlHead()
			 ->setTitle($text, $action);

		return $this;
	}

	/**
	 * @param	string	$href
	 * @param	string	$target
	 * @return	HtmlPage
	 */
	public function setHeadBase($href = null, $target = null)
	{
		$this->getHtmlHead()
			 ->setBase($href, $target);

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
		$this->getHtmlHead()
			 ->addMeta($name, $content, $httpEquiv, $charset);

		return $this;
	}

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HtmlPage
	 */
	public function addHeadMetaTag(GenericTagInterface $tag)
	{
		$this->getHtmlHead()
			 ->addMetaTag($tag);

		return $this;
	}

	/**
     * @param   string  $href   url or file path to resource  
     * @param   string  $rel    relationship between current doc and link
     * @param   string  $type   mime type
	 * @return	HtmlPage
	 */
	public function addHeadLink($src, $rel = null, $type = null)
	{
		$this->getHtmlHead()
			 ->addCssTag($src, $rel, $type);

		return $this;
	}

	/**
	 * @param	string	$content	
	 * @param	string	$type
	 * @param	string	$sep
	 * @return	HtmlPage
	 */
	public function addHeadStyle($content, $type = null, $sep = null)
	{
		$tag = $this->getTagFactory()
					->createStyleTag($content, $type, $sep);

		$this->getHtmlHead()
			 ->addCssTag($tag);

		return $this;
	}

	/**
	 * @param	string	$content
	 * @return	HtmlPage
	 */
	public function addHeadInlineStyle($content)
	{
		$this->getHtmlHead()
			 ->addInlineStyleContent($content);

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
	 * @param	string	$src
	 * @return	HtmlPage
	 */
	public function addHeadScript($src)
	{
		$this->getHtmlHead()
			 ->addScript($src);

		return $this;
	}

	/**
	 * @param	string	$src
	 * @return	HtmlPage
	 */
	public function addHeadInlineScript($content)
	{
		$this->getHtmlHead()
			 ->addInlineScriptContent($content);

		return $this;
	}

	/**
	 * @param	string	$name
	 * @param	string	$value
	 * @return	HtmlPage
	 */
	public function addBodyAttribute($name, $value = null)
	{
		$this->getHtmlBody()
			 ->addAttribute($name, $value);

		return $this;
	}

	/**
	 * @param	string	$content
	 * @return	HtmlPage
	 */
	public function addMarkup($content, $action = 'append')
	{
		$this->getHtmlBody()
			 ->addMarkup($content, $action);

		return $this;
	}

	/**
	 * @param	string|GenericTagInterface	$src
	 * @return	HtmlPage
	 */
	public function addBodyScript($src)
	{
		$this->getHtmlBody()
			 ->addScript($src);

		return $this;
	}

	/**
	 * @param	string	$content	
	 * @param	string	$action
	 * @return	HtmlPage
	 */
	public function addToBodyInlineScript($content, $action = 'append')
	{
		$this->getHtmlBody()
			 ->addInlineScriptContent($content, $action);

		return $this;
	}

	/**
	 * @param	ViewInterface $view
	 * @return	HtmlPage
	 */
	public function setView($view)
	{
		$key = $this->getContentKey();
		if ($view instanceof ViewInterface) {
			return $this->addTemplate($key, $view);
		}
		else if (is_string($view) || 
				(is_object($view) && is_callable(array($view, '__toString')))){
			$this->view = (string) $view;
			$this->removeTemplate($key);
			return $this;
		}

		return $this;
	}

	/**	
	 * @return	ViewInterface | string
	 */
	public function getView()
	{
		$key = $this->getContentKey();
		if ($this->isTemplate($key)) {
			return $this->getTemplate($key);
		}

		return $this->view;
	}

	/**
	 * @param	string	$label
	 * @param	mixed	$value
	 * @return	HtmlPage
	 */
	public function assign($name, $value)
	{
		$key = $this->getContentKey();
		if ($this->isTemplate($key)) {
			$name = "{$key}.{$name}";
			return $this->assignTo($name, $value);
		}
		
		return parent::assign($name, $value);
	}

	/**
	 * @param	string	$name
	 * @param	mixed	$default
	 * @return	mixed
	 */
	public function get($name, $default = null)
	{
		$key = $this->getContentKey();
		if ($this->isTemplate($key)) {
			return $this->getTemplate($key)
						->get($name, $default);
		}

		return parent::get($name, $default);
	}

	/**
	 * @param	string	$name
	 * @return	bool
	 */
	public function isAssigned($name)
	{
		$key = $this->getContentKey();
		if ($this->isTemplate($key)) {
			return $this->getTemplate($key)
						->isAssigned($name);
		}
		
		return parent::isAssigned($name);
	}

	/**
	 * @return	string
	 */
	public function build()
	{
		$template = $this->getTemplate('htmldoc');
		$htmlTag = $this->getHtmlTag();
		$template->assign('html-attrs', $htmlTag->getAttributeString());

		$head = $this->getHtmlHead();
		$body = $this->getHtmlBody();
		
		$isCss = $this->isCss();
		if (! $isCss) {
			$head->disableCss();
		}

		$isJs  = $this->isJs();
		if (! $isJs) {
			$head->disableJs();
			$body->disableJs();
		}
		
		$headTag = $head->configure();
		$template->assign('head-attrs', $headTag->getAttributeString());
		$template->assign('head-title', $headTag->getTitle());
		if ($headTag->isBase()) {
			$template->assign('head-base', $headTag->getBase());
		}

		if ($headTag->isMeta()) {
			$template->assign('head-meta', $headTag->getMeta());
		}
		
		if ($isCss  && $headTag->isCssTags()) {
			$template->assign('head-css', $headTag->getCssTags());
		}

		$bodyTag = $body->configure();
		$template->assign('body-attrs', $bodyTag->getAttributeString());

		$view = $this->getView();
		if ($view instanceof ViewInterface) {
			$view = $view->build();
		}
		$template->assign('body-markup', $view);

		if ($isJs) {
			$template->assign('body-js', $body->getScripts());
		}

		return $template->build();
	}

	/**
	 * @return	string
	 */
	public function getContentKey()
	{
		return $this->contentKey;
	}

	/**
	 * @param	string	$key
	 * @return	HtmlPage
	 */
	public function setContentKey($key)
	{
		if (! is_string($key) || ! ($key = trim($key))) {
			$err = 'content key must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->contentKey = $key;
	}

	/**
	 * @param	HtmlTagFactoryInterface	$factory
	 * @return	null
	 */
	protected function setTagFactory(HtmlTagFactoryInterface $factory)
	{
		$this->tagFactory = $factory;
	}

	protected function getTagFactory()
	{
		return $this->tagFactory;
	}
}
