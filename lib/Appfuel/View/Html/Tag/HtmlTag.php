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

use RunTimeException,
	InvalidArgumentException;

/**
 */
class HtmlTag extends GenericTag implements HtmlTagInterface
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
	public function __construct(HeadTagInterface $head = null,
								GenericTagInterface $body = null)
	{
		$content = $this->createTagContent(null, PHP_EOL);
		$attrs = $this->createTagAttributes(array('manifest'));
		
		parent::__construct('html', $content, $attrs);

		if (null === $head) {
			$head = new HeadTag();
		}
		$this->setHead($head);

		if (null === $body) {
			$body = new BodyTag();
		}
		$this->setBody($body);
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
	public function setHead(HeadTagInterface $tag)
	{
		if ('head' !== $tag->getTagName()) {
			$err = 'tag must have a tag name of -(head)';
			throw new InvalidArgumentException($err);
		}

		$this->head = $tag;
		return $this;
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

	public function build()
	{
		$content = $this->getTagContent();
		$content->add($this->getHead());
		$content->add($this->getBody());

		return $this->buildTag($content, $this->getTagAttributes());
	}
}
