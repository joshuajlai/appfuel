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
	Appfuel\View\FileViewTemplate,
	Appfuel\Kernel\PathFinderInterface;

/**
 * Template used to generate generic html documents
 */
class HtmlViewTemplate extends FileViewTemplate
{
	/**
	 * @param	string	$viewTplFile
	 * @param	string	$jsTpl
	 * @return	HtmlViewTemplate
	 */
	public function __construct($tpl, PathFinderInterface $pathFinder = null)
	{
		parent::__construct($tpl, $pathFinder);
		
	}

	/**
	 * @return	string
	 */
	public function getHtmlPageClass()
	{
		return $this->htmlPageClass;
	}

	/**
	 * @param	string	$file
	 * @return	HtmlViewTemplate
	 */
	public function setHtmlPageClass($class)
	{
		if (! is_string($class) || ! ($class = trim($class))) {
			$err = 'html page class must be an non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->htmlPageClass = $class;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getHtmlDocTpl()
	{
		return $this->htmlDocTpl;
	}

	/**
	 * @param	string	$file
	 * @return	HtmlView
	 */
	public function setHtmlDocTpl($file)
	{
		if (! is_string($file) || ! ($file = trim($file))) {
			$err = 'html doc template file path must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->htmlDocTpl = $file;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getTagFactoryClass()
	{
		return $this->tagFactoryClass;
	}

	/**
	 * @param	string	$file
	 * @return	HtmlViewTemplate
	 */
	public function setTagFactoryClass($class)
	{
		if (! is_string($class) || ! ($class = trim($class))) {
			$err = 'html page class must be an non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->tagFactoryClass = $class;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getLayoutClass()
	{
		return $this->layoutClass;
	}

	/**
	 * @param	string	$file
	 * @return	HtmlViewTemplate
	 */
	public function setLayoutClass($class)
	{
		if (! is_string($class) || ! ($class = trim($class))) {
			$err = 'layout class must be an non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->layoutClass = $class;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getInlineJsKey()
	{
		return $this->inlineJsKey;
	}

	/**
	 * @param	string	$key
	 * @return	HtmlViewTemplate
	 */
	public function setInlineJsKey($key)
	{
		if (! is_string($key) || empty($key)) {
			$err = 'inline js key must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		
		if (false !== strpos($key, '.')) {
			$err = 'inline is key can not have any "." characters';	
			throw new InvalidArgumentException($err);
		}

		$this->inlineJsKey = $key;
		return $this;
	}

	/**
	 * @return	ViewInterface | null when none exist
	 */
	public function getInlineJsTemplate()
	{
		return $this->getTemplate($this->getInlineJsKey());
	}

	/**
	 * @param	ViewInterface	$template
	 * @return	HtmlViewTemplate
	 */
	public function setInlineJsTemplate($template)
	{
		if (is_string($template)) {
			$template = new FileViewTemplate($template);
		}
		else if (! ($template instanceof ViewInterface)) {
			$err  = 'inline js template must be a string (path to tpl) or ';
			$err .= 'object that implments Appfuel\View\ViewInterface';
			throw new InvalidArgumentException($err);
		}

		$key = $this->getInlineJsKey();
		return $this->addTemplate($key, $template);
	}

	/**
	 * @return	bool
	 */
	public function isInlineJsTemplate()
	{
		return $this->isTemplate($this->getInlineJsKey());
	}

	/**
	 * @return	string
	 */
	public function buildInlineJs()
	{
		$template = $this->getInlineJsTemplate();
		
		$result = '';
		if ($template instanceof ViewInterface) {
			$result = $template->build();
		}
	
		return $result;
	}

	/**
	 * @param	string	$label
	 * @param	mixed	$value
	 * @return	HtmlViewTemplates
	 */
	public function assignInlineJs($label, $value)
	{
		return $this->assign("{$this->getInlineJsKey()}.{$label}", $value);
	}
}
