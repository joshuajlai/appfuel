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

use Appfuel\View\ViewCompositeTemplate;

/**
 * Template used to generate generic html documents
 */
class HtmlDocTemplate extends ViewTemplate implements HtmlDocTemplateInterface
{
	/**
	 * Defaults to the appfuel template file 
	 *
	 * @param	string				$filePath	relative path to template file
	 * @param	PathFinderInterface $pathFinder	resolves the relative path
	 * @param	array				$data		data to be assigned
	 * @return	HtmlDocTemplate
	 */
	public function __construct( $finder = null,
								array $data = null)
	{
		if (null === $filePath) {
			$filePath = 'appfuel/html/doc/standard.phtml';
		}
		$formatter = new HtmlDocFormatter($filePath);

		parent::__construct($data, $formatter);
	}

}
