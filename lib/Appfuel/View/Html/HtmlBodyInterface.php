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

use Appfuel\View\Html\Tag\BodyTag,
	Appfuel\View\Html\Tag\ScriptTag,
	Appfuel\View\Html\Tag\GenericTagInterface;

/**
 * Template used to generate generic html documents
 */
interface HtmlBodyInterface
{
	/**
	 * @return	HtmlTagInterface
	 */
	public function getBodyTag();

	/**
	 * @param	HtmlDocInterface $doc
	 * @return	HtmlBodyInterface
	 */
	public function setBodyTag(GenericTagInterface $tag);

    /** 
     * @param   string  $name
     * @param   string  $value
	 * @return	HtmlBodyInterface
     */
    public function addAttribute($name, $value = null);

    /**
     * @param   string  $name
     * @param   string  $default
     * @return  mixed
     */
    public function getAttribute($name, $default = null);

    /**
     * @param   string  $name
     * @return  bool
     */
    public function isAttribute($name);
	
	/**
	 * @return	bool
	 */
	public function isJs();

	/**
	 * @return	HtmlBody
	 */
	public function enableJs();

	/**
	 * @return	HtmlBody
	 */
	public function disableJs();

    /**
     * @return  ScriptTag
     */
    public function getInlineScriptTag();

    /**
     * @param   GenericTagInterface  $tag
     * @return  HtmlBody
     */
    public function setInlineScriptTag(GenericTagInterface $tag);

    /**
     * @param   mixed   string | object supporting __toString
     * @return  HtmlBody
     */
    public function addInlineScriptContent($data);

    /**
     * @return  string
     */
    public function getInlineScriptContentString();

    /**
     * @param   mixed   string | object supporting __toString
     * @return  HtmlBody
     */
    public function addMarkup($data);

    /**
     * @param   mixed   $src
     * @return  HtmlBody
     */
    public function addScript($src);

   /**
     * @return  array
     */
    public function getScripts();

    /**
     * @return  int
     */
    public function getScriptCount();

    /**
     * @param   int $index 
     * @return  string | array
     */
    public function getMarkup($index = null);

    /**
     * @return  string  
     */
    public function getMarkupString();

    /**
     * @return  string
     */
    public function build();
}
