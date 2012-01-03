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
	Appfuel\View\Html\Tag\HtmlTag,
	Appfuel\View\Html\Tag\ScriptTag,
	Appfuel\View\Html\Tag\HtmlTagInterface,
	Appfuel\View\Html\Tag\GenericTagInterface;

/**
 * Template used to generate generic html documents
 */
class HtmlPage extends ViewTemplate implements HtmlPageInterface
{
	/**
	 * @var HtmlTagInterface
	 */
	protected $html = null;

	/**
	 * Key used to add and get the view template
	 * @var string
	 */
	protected $contentKey = 'content';

	/**
	 * Name of the inline javascript template
	 * @var GenericTagInterface
	 */
	protected $inlineJs = null;

	/**
	 * @param	FileViewInterface $content	main html body content
	 * @param	FileViewInterface $inlineJs inline js content 
	 * @param	HtmlDocInterface  $doc		html doc template
	 * @return	HtmlPage
	 */
	public function __construct(ViewInterface $template,
								GenericTagInterface $inlineJs = null,
								HtmlTagInterface  $doc = null,
								$contentKey = null)
	{
		if (null !== $contentKey) {
			$this->setContentKey($contentKey);
		}

		$this->setView($template);

		if (null === $inlineJs) {
			$inlineJs = new ScriptTag();
		}
		$this->setInlineJs($inlineJs);

		if (null == $doc) {
			$doc = new HtmlTag();
		}
		$this->setHtmlTag($doc);
	}

	/**
	 * @return	string
	 */
	public function getContentKey()
	{
		return $this->contentKey;
	}

	/**
	 * @return	HtmlTagInterface
	 */
	public function getHtmlTag()
	{
		return $this->html;
	}

	/**
	 * @param	HtmlDocInterface $doc
	 * @return	HtmlPage
	 */
	public function setHtmlTag(HtmlTagInterface $tag)
	{
		$this->html = $tag;
		return $this;
	}

	/**
	 * @param	ViewInterface $view
	 * @return	HtmlPage
	 */
	public function setView(ViewInterface $view)
	{
		$this->addTemplate($this->getContentKey(), $view);
		return $this;
	}

	/**	
	 * @return	ViewInterface
	 */
	public function getView()
	{
		return $this->getTemplate($this->getContentKey());
	}

	/**
	 * @param	GenericTagInterface $script
	 * @return	HtmlPage
	 */
	public function setInlineJs(GenericTagInterface $script)
	{
		if ('script' !== $script->getTagName()) {
			$err = 'script tag must have a tag name of -(script)';
			throw new InvalidArgumentException($err);
		}

		if ($script->isAttribute('src')) {
			$err = 'an inline script can not have a src attribute';
			throw new InvalidArgumentException($err);
		}

		$this->inlineJs = $script;
		return $this;
	}

	/**	
	 * @return	GenericTagInterface
	 */
	public function getInlineJs()
	{
		return $this->inlineJs;
	}

	/**
	 * @return	string
	 */
	public function build()
	{
		$html = $this->getHtmlTag();
		$body = $html->getBody();
	
		$view = $this->getView();
		$inlineJs = $this->getInlineJs();

		$content = array($view->build(), $inlineJs->build());
		$body->loadContent($content);
		return $html->build();
	}

	/**
	 * @param	string	$key
	 * @return	HtmlPage
	 */
	protected function setContentKey($key)
	{
		if (! is_string($key) || ! ($key = trim($key))) {
			$err = 'content key must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->contentKey = $key;
	}
}
