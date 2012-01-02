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
 * Create html tag objects
 */
class HtmlTagFactory implements HtmlTagFactoryInterface
{
	/**
	 * @param	string	$tagName
	 * @param	TagContentInterface	$content	
	 * @param	TagAttributesInterface $attrs
	 * @param	bool	$isTagNameLocked
	 * @return	GenericTag
	 */
	public function createGenericTag($name, 
									 TagContentInterface $content = null,
									 TagAttributesInterface $attrs = null,
									 $isTagNameLocked = true)
	{
		return new GenericTag($name, $content, $attrs, $isTagNameLocked);
	}

	/**
	 * @param	HeadTagInterface $head
	 * @param	GenericTagInterface $body
	 * @return	HtmlTag
	 */
	public function createHtmlTag(HeadTagInterface $head = null,
								  GenericTagInterface $body = null)
	{
		return new HtmlTag($head, $body);
	}

	/**
	 * @param	string	$contentSeparator
	 * @return	HeadTag
	 */
	public function createHeadTag($contentSep = PHP_EOL)
	{
		return new HeadTag($contentSep);
	}

	/**
	 * @param	mixed	string | array of strings $content
	 * @param	string	$contentSep	
	 * @return	BodyTag
	 */
	public function createBodyTag($content = null, $contentSep = PHP_EOL)
	{
		return new BodyTag($content, $contentSep);
	}
}
