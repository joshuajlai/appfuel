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

use Appfuel\View\ViewTemplate,
	Appfuel\View\Formatter\FileFormatter;

/**
 * Template used to generate generic html documents
 */
class HtmlView extends ViewTemplate
{
	/**
	 * @param	string				$path	relative path to template file
	 * @param	array				$data	data to be assigned
	 * @return	HtmlTemplate
	 */
	public function __construct(HtmlDocTemplateInterface $doc = null)
	{
		parent::__construct($data, new FileFormatter());
		
		if (null === $path) {
			$path = 'ui/appfuel/html';
		}

		/* 
		 * any file path will be relative to this root path and
		 * this path is relative to AF_BASE_PATH
		 */
		$this->setRootPath($path);
				
	}
}
