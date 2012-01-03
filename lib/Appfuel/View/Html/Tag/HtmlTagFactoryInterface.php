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
    public function createHeadTag($contentSep = PHP_EOL);

    /**
	 * @param	mixed	string | array of strings $content
     * @param   string  $contentSeparator
     * @return  HeadTagInterface
     */
    public function createBodyTag($content = null, $contentSep = PHP_EOL);

    /**
     * @param   mixed   string | array of strings $content
     * @param   string  $contentSep 
     * @return  TitleTag
     */
    public function createTitleTag($content = null, $contentSep = ' ');

	/**
     * @param   string  $href   url or file path to resource  
     * @param   string  $rel    relationship between current doc and link
     * @param   string  $type   mime type
     * @return  LinkTag
	 */
	public function createLinkTag($href, $rel = null, $type = null);

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
                                  $charset = null);

	/**
     * @param   string  $src    
     * @param   mixed   $data
     * @param   string  $sep
     * @param   string  $type
     * @return  ScriptTag
     */
    public function createScriptTag($src = null,
                                    $data = null,
                                    $sep  = null,
                                    $type = null);
	
	/**
     * @param   mixed   $data
     * @param   string  $type
     * @param   string  $sep
     * @return  StyleTag
     */
	public function createStyleTag($data = null, $type = null, $sep = null);
}
