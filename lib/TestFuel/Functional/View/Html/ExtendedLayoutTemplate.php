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
namespace TestFuel\Functional\View\Html;

use Appfuel\View\ViewInterface,
	Appfuel\View\FileViewTemplate,
	Appfuel\View\Html\HtmlLayoutInterface; 

/**
 * Used in any unit test that needs check code that will dynamically create
 * a html layouts from a string that is the class name
 */
class ExtendedLayoutTemplate 
		extends FileViewTemplate implements HtmlLayoutInterface
{
	/** 
	 * Not a real layout so disable the constructor
	 */
	public function __construct()
	{}

    public function getViewKey(){}
    
	public function setViewKey($key){}
    
	public function getInlineJsContent(){}
   
	public function isViewTemplate()
	{
		return $this->isTemplate('view');
	}
 
	public function setView(ViewInterface $view)
	{
		$this->addTemplate('view', $view);
		return $this;
	}

    public function getView()
	{
		return $this->getTemplate('view');
	}
}
