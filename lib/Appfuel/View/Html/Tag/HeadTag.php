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
namespace Appfuel\View\Html\Tag;

/**
 * Title tag used in the html head
 */
class HeadTag extends HtmlTag
{
	/**
	 * @param	string	$data	content for the title
	 * @return	Title
	 */
	public function __construct($data = null, $sep = ' ')
	{
		$content = new TagContent($data, $sep);
		parent::__construct('title', $content);
		$this->disableRenderWhenEmpty();
	}
}
