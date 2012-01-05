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
	Appfuel\View\Html\Tag\BodyTag,
	Appfuel\View\Html\Tag\ScriptTag,
	Appfuel\View\Html\Tag\TagContent,
	Appfuel\View\Html\Tag\HtmlTagInterface,
	Appfuel\View\Html\Tag\GenericTagInterface,
	Appfuel\View\Html\Tag\HtmlTagFactoryInterface;

/**
 * Template used to generate generic html documents
 */
class HtmlBody implements HtmlBodyInterface
{
	/**
	 * @var HtmlTagFactoryInterface
	 */
	protected $factory = null;

	/**
	 * @var HtmlTagInterface
	 */
	protected $body = null;

	/**
	 * List of html content blocks to be added as the first block of content
	 * in the body tag
	 */
	protected $markup = null;

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
	public function __construct(HtmlTagFactoryInterface $factory)
	{
		$this->setBodyTag($factory->createBodyTag());

		/* 
		 * hold all html markup in a TagContent object unit we are
		 * ready to build
		 */
		$this->markup = $factory->createTagContent(null, PHP_EOL);

		/*
		 * All inline javascript is kept as one or more content blocks in
		 * one script tag
		 */
		$this->setInlineScriptTag($factory->createScriptTag());

		$this->factory = $factory;
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
	 * @param	string	$name
	 * @return	string	$value
	 */
	public function addAttribute($name, $value = null)
	{
		$this->getBodyTag()
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
		return $this->getBodyTag()
					->getAttribute($name, $default);
	}

	/**
	 * @param	string	$name
	 * @return	bool
	 */
	public function isAttribute($name)
	{
		return $this->getBodyTag()
					->isAttribute($name);
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
	 * @param	mixed	string | object supporting __toString
	 * @return	HtmlBody
	 */
	public function addMarkup($data)
	{
		$this->getMarkupContent()
			 ->add($data);

		return $this;
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
	 * @param	int	$index 
	 * @return	string | array
	 */
	public function getMarkup($index = null)
	{
		return $this->getMarkupContent()
					->get($index);
	}

	/**
	 * @return	string	
	 */
	public function getMarkupString()
	{
		return $this->getMarkupContent()
					->build();
	}

	/**
	 * @return	string
	 */
	public function build()
	{
		$body = $this->getBodyTag();
		$markup = $this->getMarkupString();
		$body->addContent($markup, 'prepend');

		/* add the inline script as the last script tag */
		$this->addScript($this->getInlineScriptTag());
		$scripts = $this->getScripts();
		foreach ($scripts as $scriptTag) {
			$body->addContent($scriptTag);
		}
	
		return $body->build();
	}

	public function configure(GenericTagInterface $body = null)
	{
		if (null === $body) {
			$body = $this->getBodyTag();
		}
        else if ('body' !== $body->getTagName()) {
            $err = 'configure failed: body tag must have a tag name of -(body)';
            throw new InvalidArgumentException($err);
        }
		$markup = $this->getMarkupString();
		$body->addContent($markup, 'prepend');

		/* add the inline script as the last script tag */
		$this->addScript($this->getInlineScriptTag());
		$scripts = $this->getScripts();
		foreach ($scripts as $scriptTag) {
			$body->addContent($scriptTag);
		}

		return $body;
	}

	/**
	 * @return	HtmlTagFactoryInterface
	 */
	protected function getTagFactory()
	{
		return $this->factory;	
	}

	/**
	 * @return	TagContent
	 */
	protected function getMarkupContent()
	{
		return $this->markup;
	}
}
