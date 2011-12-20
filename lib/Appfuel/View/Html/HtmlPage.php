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

use Appfuel\View\Compositor\TextCompositor,
	Appfuel\View\ViewTemplate,
	Appfuel\View\ViewTemplateInterface;

/**
 * Template used to generate generic html documents
 */
class HtmlPage extends ViewTemplate
{
	/**
	 * Assign a FileFormatter because html templates generally require a
	 * .phtml template file. Also set the root path to be appfuel
	 * 
	 * @param	string				$path	relative path to template file
	 * @param	array				$data	data to be assigned
	 * @return	HtmlTemplate
	 */
	public function __construct(HtmlDocTemplateInterface $doc = null,
								ViewTemplateInterface $content = null)
	{
		if (null == $doc) {
			$doc = new HtmlDocTemplate();
		}

		if (null === $content) {
			$content = new ViewTemplate();
		}

		$this->addTemplate('htmldoc', $doc)
			 ->addTemplate('content', $content);

		$this->assignTemplate('content', 'htmldoc', 'html-body-content')
			 ->assignTemplate('htmldoc');

		parent::__construct(null, new TextCompositor(null,null, 'values'));
	}
}
