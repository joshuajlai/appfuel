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
	public function __construct(ViewTemplateInterface $content,
								HtmlDocTemplateInterface $doc = null)
	{
		if (null == $doc) {
			$doc = new HtmlDocTemplate();
		}

		$this->addTemplate('htmldoc', $doc)
			 ->addTemplate('content', $content);

		$this->assignTemplate('content', 'htmldoc', 'body-content')
			 ->assignTemplate('htmldoc');

		$data = null;
		$viewCompositor = new TextCompositor(null, null, 'values');
		parent::__construct($data, $viewCompositor);
	}
}
