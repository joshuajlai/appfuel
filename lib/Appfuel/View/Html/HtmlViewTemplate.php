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
	Appfuel\View\FileViewTemplate,
	Appfuel\Kernel\PathFinderInterface;

/**
 * Template used to generate generic html documents
 */
class HtmlViewTemplate extends FileViewTemplate implements HtmlViewInterface
{
	/**
	 * This is used to override the default Appfuel\View\Html\HtmlDocTemplate
	 * @var string
	 */
	protected $htmlDocClass = null;

	/**
	 * The class your html view belongs to this is optional
	 * @var string
	 */
	protected $layoutClass = null;

	/**
	 * This is used to overide the default Appfuel\View\HtmlPageTemplate
	 * @var string
	 */
	protected $htmlPageClass = null;

	/**
	 * Each html view uses a javascript template to intialize the page 
	 * @var string
	 */
	protected $jsFile = null;

	/**
	 * @param	string	$tpl
	 * @param	string	$jsTpl 
	 * @return	HtmlViewTemplate
	 */
	public function __construct($tpl, 
								$jsFile = null, 
								PathFinderInterface $pathFinder = null)
	{
		parent::__construct($tpl, $pathFinder);

		if (null !== $jsFile) {
			$this->setJsFile($jsFile);
		}
	}

	/**
	 * @return	string
	 */
	public function getJsFile()
	{
		return $this->jsFile;
	}

	/**
	 * @param	string	$file
	 * @return	HtmlViewTemplate
	 */
	public function setJsFile($file)
	{
		if (! is_string($file) || ! ($file = trim($file))) {
			$err = 'javascript template file path must be an non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->jsFile = $file;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getHtmlDocClass()
	{
		return $this->htmlDocClass;
	}

	/**
	 * @param	string	$file
	 * @return	HtmlViewTemplate
	 */
	public function setHtmlDocClass($class)
	{
		if (! is_string($class) || ! ($class = trim($class))) {
			$err = 'html doc class must be an non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->htmlDocClass = $class;
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
}
