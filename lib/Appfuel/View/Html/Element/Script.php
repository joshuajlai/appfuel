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
 * 
 */
class Script extends Tag
{
	/**
	 * @param	string	$data	content for the title
	 * @return	Title
	 */
	public function __construct($content)
	{
		$valid = array(
			'async',
			'charset',
			'defer',
			'src',
			'type'
		);
		$this->setTagName('script')
			 ->addValidAttributes($valid);

		$this->addAttribute('type', 'text/javascript');

		if ($this->isValidString($content)) {
			$this->addContent($content);
		}
	}
}
