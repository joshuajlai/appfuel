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
namespace Appfuel\App\View\Html;

use Appfuel\Framework\Exception,
	Appfuel\Framework\FileInterface,
	Appfuel\Framework\View\TemplateInterface,
	Appfuel\App\View\Template,
	Appfuel\View\Html\Element\Title;

/**
 * Html document template. Used to manage the html document. This template 
 * does not act on any content inside the body tag itself. 
 */
class Doc extends Template implements TemplateInterface
{
	/**
	 * Title tag used in the head of the document
	 * @var Title
	 */
	protected $title = null;

	public function __construct()
	{
		$this->addFile('markup', 'doc/standard.phtml')
			 ->addFile('inline-js', 'doc/init.pjs');
	}

	/**
	 * @return Title
	 */
	public function getTitleTag()
	{
		return $this->title;
	}

	/**
	 * @param	Title $tile
	 * @return	Doc
	 */
	public function setTitle(Title $title)
	{
		$this->title = $title;
		return $this;
	}

	public function build()
	{
		/*
		 * The layout hold all the html content so there is not much
		 * sense building without it. This will produce an empty html
		 * document
		 */
		if (! $this->isLayout()) {
			return $this->buildFile('markup');
		}

		$layout  = $this->getLayout();
		$this->assign('layout-content', $layout->build()); 

		if ($this->fileExists('inline-css')) {
			$css = $this->buildFile('inline-css');
			$this->assign('inline-css', $css);
		}

		$inlineJs = '';
		if ($this->fileExists('inline-js')) {
		
		}
	}
}
