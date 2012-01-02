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

use RunTimeException,
	InvalidArgumentException;

/**
 */
interface HtmlTagInterface extends GenericTagInterface
{
	/**
	 * @return	HtmlTagInterface
	 */
	public function getHead();

	/**
 	 * Requirements
	 * 1) the tag name must be 'head'
	 * 2) fluent interface
	 * 
	 * @param	HtmlHeadTagInterface $head
	 * @return	HtmlTagInterface
	 */
	public function setHead(GenericTagInterface $tag);

	/**
	 * @return	GenericTagInterface
	 */
	public function getBody();

	/**
 	 * Requirements
	 * 1) the tag name must be 'head'
	 * 2) fluent interface
	 * 
	 * @param	GenericTagInterface $tag
	 * @return	HtmlTagInterface
	 */
	public function setBody(GenericTagInterface $tag);
}
