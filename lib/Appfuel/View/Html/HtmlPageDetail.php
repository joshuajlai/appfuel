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
	Appfuel\View\ViewInterface,
	Appfuel\View\Html\Tag\HtmlTagFactoryInterface;

/**
 * Value object used to describe the properties needed by the html page 
 * builder and configure an html page.
 */
class HtmlPageDetail implements HtmlPageDetailInterface
{
	/**
	 * Used to create an alternate implementation of HtmlPageInterface
	 * @var string	
	 */
	protected $pageClass = null;

	/**
	 * Location of the html doc template file
	 * @var string
	 */
	protected $htmlDoc = null;

	/**
	 * Location to file that holds and array of configuration options
	 * for the html page to be build.
	 * @var string
	 */ 
	protected $htmlConfig = null;

	/**
	 * Used by the page builder to create an alternate html tag factory for
	 * the HtmlPageInterface
	 * @var	string
	 */
	protected $tagFactory = null;

	/**
	 * The class your html view belongs to this is optional
	 * @var string
	 */
	protected $layout = null;

	/**
	 * Class name of the inline js template to use
	 * @var string
	 */
	protected $inlineJs = null;

	/**
	 * View teplate class or object for the page
	 * @var string | ViewTemplateInterface
	 */
	protected $view = null;

	/**
	 * @param	string	$viewTplFile
	 * @param	string	$jsTpl
	 * @return	HtmlViewTemplate
	 */
	public function __construct(array $detail)
	{
		if (isset($detail['html-page-class'])) {
			$this->setHtmlPageClass($detail['html-page-class']);
		}

		if (isset($detail['html-doc'])) {
			$this->setHtmlDoc($detail['html-doc']);
		}

		if (isset($detail['html-config'])) {
			$this->setHtmlConfig($detail['html-config']);
		}

		if (isset($detail['tag-factory'])) {
			$this->setTagFactory($detail['tag-factory']);
		}

		if (isset($detail['layout-template'])) {
			$this->setLayoutTemplate($detail['layout-template']);
		}

		if (isset($detail['inline-js-template'])) {
			$this->setInlineJs($detail['inline-js-template']);
		}

		if (isset($detail['view-template'])) {
			$this->setViewTemplate($detail['view-template']);
		}
	}

	/**
	 * @return	bool
	 */
	public function isHtmlPageClass()
	{
		return ! empty($this->pageClass);
	}

	/**
	 * @return	string
	 */
	public function getHtmlPageClass()
	{
		return $this->pageClass;
	}

	/**
	 * @return	bool
	 */
	public function isHtmlDoc()
	{
		$htmlDoc = $this->htmlDoc;
		return is_string($htmlDoc) || $htmlDoc instanceof ViewInterface;
	}

	/**
	 * @return	string
	 */
	public function getHtmlDoc()
	{
		return $this->htmlDoc;
	}

	/**
	 * @return	bool
	 */
	public function isHtmlConfig()
	{
		$config = $this->htmlConfig;
		return is_string($config) || is_array($config);
	}

	/**
	 * @return	string
	 */
	public function getHtmlConfig()
	{
		return $this->htmlConfig;
	}

	/**
	 * @return	bool
	 */
	public function isTagFactory()
	{
		$factory = $this->tagFactory;
		return is_string($factory) || 
			   $factory instanceof HtmlTagFactoryInterface;
	}

	/**
	 * @return	string
	 */
	public function getTagFactory()
	{
		return $this->tagFactory;
	}

	/**
	 * @return	bool
	 */
	public function isLayoutTemplate()
	{
		$layout = $this->layout;
		return is_string($layout) || 
			   $layout instanceof ViewInterface;
				
	}

	/**
	 * @return	string
	 */
	public function getLayoutTemplate()
	{
		return $this->layout;
	}

	/**
	 * @return	bool
	 */
	public function isInlineJsTemplate()
	{
		return is_string($this->inlineJs) || 
			   $this->inlineJs instanceof ViewInterface;
	}

	/**
	 * @return	string
	 */
	public function getInlineJsTemplate()
	{
		return $this->inlineJs;
	}

	/**
	 * @return	bool
	 */
	public function isViewTemplate()
	{
		return is_string($this->view) || 
			   $this->view instanceof ViewInterface;
	}

	/**
	 * @return	mixed
	 */
	public function getViewTemplate()
	{
		return $this->view;
	}

	/**
	 * @param	string	$class
	 * @return	null
	 */
	protected function setHtmlPageClass($class)
	{
		if (! is_string($class) || empty($class)) {
			$err = "html page class must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->pageClass = $class;
	}

	/**
	 * @param	mixed	string|ViewInterface
	 * @return	null
	 */
	protected function setHtmlDoc($doc)
	{
		if (! is_string($doc) && !($doc instanceof ViewInterface)) {
			$err  = 'html doc must be a string (path to tpl) or an object ';
			$err .= 'that implments Appfuel\View\ViewInterface';
			throw new InvalidArgumentException($err);
		}

		$this->htmlDoc = $doc;
	}

	/**
	 * @param	string | array
	 * @return	null
	 */
	protected function setHtmlConfig($config)
	{
		if (! is_string($config) && ! is_array($config)) {
			$err  = 'html config must be a string (path to config file) or ';
			$err .= 'a non empty array of config options';
			throw new InvalidArgumentException($err);
		}
		$this->htmlConfig = $config;
	}

	/**
	 * @param	string | HtmlTagFactoryInterface
	 * @return	null
	 */
	protected function setTagFactory($factory)
	{
		if (! is_string($factory) && 
			! ($factory instanceof HtmlTagFactoryInterface)) {
			$err  = 'html tag factory must be a string (class name) or an ';
			$err .= 'object that implements Appfuel\View\Html\Tag\HtmlTag';
			$err .= 'FactoryInterface';
			throw new InvalidArgumentException($err);
		}

		$this->tagFactory = $factory;
	}

	/**
	 * @return	null
	 */
	protected function setLayoutTemplate($layout)
	{
		if (! is_string($layout) && 
			! ($layout instanceof ViewInterface)) {
			$err  = 'layout template must be a string (layout class) or ';
			$err .= 'an object that implements Appfuel\View\ViewInterface';
			throw new InvalidArgumentException($err);
		}

		$this->layout = $layout;
	}

	/**
	 * @return	null
	 */
	protected function setInlineJsPath($path)
	{
		$this->validateString($path, 'inline js template path');
		$this->inlineJsPath = $path;
	}

	/**
	 * @param	mixed	string | ViewTemplateInterface
	 * @return	null
	 */
	protected function setInlineJs($js)
	{
		if (! is_string($js) && !($js instanceof ViewInterface)) {
			$err  = 'inline js must be a string (class name) or an object ';
			$err .= 'that implments Appfuel\View\ViewInterface';
			throw new InvalidArgumentException($err);
		}
		$this->inlineJs = $js;
	}

	/**
	 * @param	string	$path
	 * @return	null
	 */
	protected function setViewPath($path)
	{
		$this->validateString($path, 'view template path');
		$this->viewPath = $path;
	}

	/**
	 * @param	string|ViewTemplateInterface	$view
	 * @return	null
	 */
	protected function setViewTemplate($view)
	{
		if (! is_string($view) && ! ($view instanceof ViewInterface)) {
			$err  = 'view must be a string (class name) or an object that ';
			$err .= 'implements Appfuel\View\ViewInterface';
			throw new InvalidArgumentException($err);
		}

		$this->view = $view;
	}
}
