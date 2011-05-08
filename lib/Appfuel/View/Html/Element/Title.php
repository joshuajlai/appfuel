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
 * Holds the common logic used to describe and build most html elements. You
 * can extended this class and in your constructor define the valid attributes,
 * use a closing tag or not. You can override the build and customize the way
 * the tag is rendered.
 */
class Title extends Tag
{
	public function __construct($data)
	{
		$this->setTagName('title')
			 ->disableAttributes();

		if ($this->isValidString($data)) {
			$this->addContent($data);
		}
	}
}
