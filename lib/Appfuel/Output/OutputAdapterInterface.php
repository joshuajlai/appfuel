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
namespace Appfuel\Output;

/**
 * Strategy used to handle the details of rendering content. The two most 
 * popular strategies are http and console
 */
interface OutputAdapterInterface
{
	public function render($data);
	public function renderError($msg, $code);
}
