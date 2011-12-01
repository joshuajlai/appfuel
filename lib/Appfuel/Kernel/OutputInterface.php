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
namespace Appfuel\Kernel;

/**
 * A generalization of outputting that uses strategies in the form of 
 * output adapters to handle the act of ouputing content
 */
interface OutputInterface
{
	public function render($data);
	public function renderError($msg);
}
