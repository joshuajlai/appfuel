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
class HtmlTemplate extends ViewTemplate
{
	/**
	 * Assign a FileFormatter because html templates generally require a
	 * .phtml template file. Also set the root path to be appfuel
	 * 
	 * @param	string				$filePath	relative path to template file
	 * @param	PathFinderInterface $pathFinder	resolves the relative path
	 * @param	array				$data		data to be assigned
	 * @return	HtmlDocTemplate
	 */
	public function __construct($filePath = null, array $data = null)
	{
		parent::__construct($data, new FileFormatter());
		
		/* 
		 * any file path will be relative to this root path and
		 * this path is relative to AF_BASE_PATH
		 */
		$this->setRootPath('ui/appfuel/html');
		
	}

}
