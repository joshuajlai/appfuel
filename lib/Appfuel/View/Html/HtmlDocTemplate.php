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

use Appfuel\Framework\Exception,
	Appfuel\View\ViewCompositeTemplate,
	Appfuel\Framework\View\ViewTemplateInterface,
	Appfuel\Framework\View\Html\HtmlDocTemplateInterface;

/**
 * Template used to generate generic html documents
 */
class HtmlDocTemplate 
	extends ViewCompositeTemplate implements HtmlDocTemplateInterface
{
	/**
	 * Defaults to the appfuel template file 
	 *
	 * @param	string				$filePath	relative path to template file
	 * @param	PathFinderInterface $pathFinder	resolves the relative path
	 * @param	array				$data		data to be assigned
	 * @return	HtmlDocTemplate
	 */
	public function __construct($filePath = null,
								PathFinderInterface $finder = null,
								array $data = null)
	{
		if (null === $filePath) {
			$filePath = 'html/doc/standard.phtml';
		}

		parent::__construct($filePath, $finder, $data);
	}

}
