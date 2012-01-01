<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\View\Html\Tag;

use RunTimeException;

/**
 */
class HtmlTag extends GenericTag
{
	/**
	 * @var GenericTagInterface
	 */
	protected $head = null;

	/**
	 * @var GenericTagInterface
	 */
	protected $body = null;

	/**
	 * Only has two valid attributes href and target
	 *
	 * @return	base
	 */
	public function __construct()
	{
		$content = $this->createTagContent(null, PHP_EOL);
		$attrs = $this->createTagAttributes(array('manifest'));
		
		parent::__construct('html', $content, $attrs);
	}

	/**
	 * @return	GenericTagInterface
	 */
	public function getHead()
	{
		return $this->head;
	}

	/**
	 * @param	HtmlHeadTagInterface $head
	 * @return	HtmlTag
	 */
	public function setHead(GenericTagInterface $tag)
	{
		if ('head' !== $tag->getTagName()) {
			$err = 'tag must have a tag name of -(head)';
			throw new InvalidArgumentException($err);
		}

		$this->head = $head;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isHead()
	{
		return $this->head instanceof GenericTagInterface;
	}

	/**
	 * @return	GenericTagInterface
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @param	GenericTagInterface $tag
	 * @return	HtmlTag
	 */
	public function setBody(GenericTagInterface $tag)
	{
		if ('body' !== $tag->getTagName()) {
			$err = 'tag must have a tag name of -(body)';
			throw new InvalidArgumentException($err);
		}

		$this->body = $tag;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isBody()
	{
		return $this->body instanceof GenericTagInterface;
	}


	public function build()
	{
		$content = $this->getTagContent();
		if (! $this->isHead()) {
			return '';
		}
		$content->add($this->getHead());

		if ($this->isBody()) {
			$body = $this->getBody();
		}
		else {
			$body = $this->createBodyTag();
		}
		$content->add($body);

		return $this->buildTag($content, $this->getTagAttributes());
	}

	protected function createBodyTag()
	{
		return new BodyTag();
	}
}
