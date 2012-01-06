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
	Appfuel\View\Html\Tag\HtmlTagFactory,
	Appfuel\View\Html\Tag\HtmlTagFactoryInterface;


/**
 * Template used to generate generic html documents
 */
class HtmlPageBuilder implements HtmlPageBuilderInterface
{
	/**
	 * @var	HtmlTagFactoryInterface
	 */
	protected $tagFactory = null;

	/**
	 * Defaults to the appfuel template file 
	 *
	 * @param	string				$file		relative path to template file
	 * @param	PathFinderInterface $pathFinder	resolves the relative path
	 * @return	HtmlDocTemplate
	 */
	public function __construct(HtmlTagFactoryInterface $tagFactory = null)
	{
		
	}

	/**
	 * @return	string
	 */
	public function buildPage(array $data)
	{
	}
}
