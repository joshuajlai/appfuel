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
	Appfuel\View\Compositor\TextCompositor,
	Appfuel\View\Html\Tag\BodyTag,
	Appfuel\View\Html\Tag\ScriptTag,
	Appfuel\View\Html\Tag\TagContent,
	Appfuel\View\Html\Tag\HtmlTagInterface,
	Appfuel\View\Html\Tag\GenericTagInterface;

/**
 * Template used to generate generic html documents
 */
class HtmlBody extends ViewTemplate implements HtmlBodyInterface
{
	/**
	 * @var HtmlTagInterface
	 */
	protected $body = null;

	/**
	 * List of html content blocks to be added as the first block of content
	 * in the body tag
	 */
	protected $content = null;

	/**
	 * list of javascript files to be loaded at the bottom of the body tag
	 * @var array
	 */
	protected $scripts = array();

	/**	
	 * Inline javascript that will appear at the bottom the body tag below
	 * the javascript files
	 * @var GenericTagInterface
	 */
	protected $inlineScript = null;

	/**
	 * Flag used to determine if javascript is enabled for the body
	 * @var bool
	 */
	protected $isJs = true;

	/**
	 * @param	GenericTagInterface  $body	 html body tag
	 * @param	GenericTagInterface  $inline inline js script tag
	 * @return	HtmlBody
	 */
	public function __construct(GenericTagInterface $body = null,
								GenericTagInterface $script = null)
	{
		$compositor = new TextCompositor(null, null, 'values');
		parent::__construct(null, $compositor);
		
		if (null === $body) {
			$body = new BodyTag();
		}
		$this->setBodyTag($body);	
	
		$this->content = new TagContent();	
	}

	/**
	 * @return	HtmlTagInterface
	 */
	public function getBodyTag()
	{
		return $this->body;
	}

	/**
	 * @param	HtmlDocInterface $doc
	 * @return	HtmlPage
	 */
	public function setBodyTag(GenericTagInterface $tag)
	{
		if ('body' !== $tag->getTagName()) {
			$err = 'body tag must have a tag name of -(body)';
			throw new InvalidArgumentException($err);
		}

		$this->body = $tag;
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
	 * @return	TagContent
	 */
	protected function getBodyContent()
	{
		return $this->content;
	}
}
