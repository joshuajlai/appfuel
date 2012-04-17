<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Html\Tag;

use RunTimeException;

/**
 * Create html tag objects
 */
class HtmlTagFactory implements HtmlTagFactoryInterface
{

	/**
	 * @param	mixed	$data	
	 * @param	string	$char	content separator
	 * @return	TagContent
	 */
	public function createTagContent($data = null, $sep = null)
	{
		return new TagContent($data, $sep);
	}

	/**
	 * @param	mixed	$data	
	 * @param	string	$char	content separator
	 * @return	TagContent
	 */
	public function createTagAttributes(array $whiteList = null)
	{
		return new TagAttributes($whiteList);
	}

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

	/**
	 * @param	mixed	string | array of strings $content
	 * @param	string	$contentSep	
	 * @return	TitleTag
	 */
	public function createTitleTag($content = null, $contentSep = ' ')
	{
		return new TitleTag($content, $contentSep);
	}

	/**
	 * @param	string	$href
	 * @param	string	$target
	 * @return	BaseTag
	 */
	public function createBaseTag($href = null, $target = null)
	{
		return new BaseTag($href, $target);
	}

    /**
     * @param   string  $href   url or file path to resource  
     * @param   string  $rel    relationship between current doc and link
     * @param   string  $type   mime type
     * @return  LinkTag
     */
    public function createLinkTag($href, $rel = null, $type = null)
	{
		return new LinkTag($href, $rel, $type);
	}

    /**
     * @param   string  $name
     * @param   string  $content
     * @param   string  $httpEquiv
     * @param   string  $charset
     * @return  MetaTag
     */
    public function createMetaTag($name = null,
								 $content = null,
								 $httpEquiv = null,
								 $charset = null)
	{
		return new MetaTag($name, $content, $httpEquiv, $charset);
	}

	/**
	 * @param	string	$src	
	 * @param	mixed	$data
	 * @param	string	$sep
	 * @param	string	$type
	 * @return	ScriptTag
	 */
	public function createScriptTag($src = null, 
									$data = null,
									$sep  = null,
									$type = null)
	{
		return new ScriptTag($src, $data, $sep, $type);
	}

	/**
	 * @param	mixed	$data
	 * @param	string	$type
	 * @param	string	$sep
	 * @return	StyleTag
	 */
	public function createStyleTag($data = null, $type = null, $sep = null)
	{
		return new StyleTag($data, $type, $sep);
	}
}
