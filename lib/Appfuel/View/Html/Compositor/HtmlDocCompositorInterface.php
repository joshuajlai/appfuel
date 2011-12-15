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
namespace Appfuel\View\Html\Compositor;

/**
 */
interface HtmlDocCompositorInterface extends HtmlCompositorInterface
{
	/**
	 * @param	string	$type
	 * @return	null
	 */
	public function renderDocType($type);

	/**
	 * @param	string	$type
	 * @return	string
	 */
	public function getDocType($type);
}
