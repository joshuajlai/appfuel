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
namespace Appfuel\View\Html\Element;

use Appfuel\Framework\Exception;

/**
 * The html 5 title tag supports global attributes
 */
class Title extends Tag
{
	/**
	 * @param	string	$data	content for the title
	 * @return	Title
	 */
	public function __construct($data = null)
	{
		$this->setTagName('title');

		if ($this->isValidString($data)) {
			$this->addContent($data);
		}
	}
}
