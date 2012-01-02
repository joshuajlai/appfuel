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

/**
 * Create html tag objects
 */
interface HtmlTagFactoryInterface
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
									 $isTagNameLocked = true);

	/**
	 * @param	HeadTagInterface $head
	 * @param	GenericTagInterface $body
	 * @return	HtmlTag
	 */
	public function createHtmlTag(HeadTagInterface $head = null,
								  GenericTagInterface $body = null);

    /**
     * @param   string  $contentSeparator
     * @return  HeadTagInterface
     */
    public function createHeadTag($contentSeparator = PHP_EOL);

    /**
	 * @param	mixed	string | array of strings $content
     * @param   string  $contentSeparator
     * @return  HeadTagInterface
     */
    public function createBodyTag($content = null, $contentSeparator = PHP_EOL);


}
