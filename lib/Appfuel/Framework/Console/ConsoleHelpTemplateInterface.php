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
namespace Appfuel\Framework\Console;

/**
 */
interface ConsoleHelpTemplateInterface extends ConsoleViewTemplateInterface
{
	public function getStatusCode();
	public function setStatusCode($code);
	public function getStatusText();
	public function setStatusText($text);
	public function setStatus($code, $text);
	public function enableErrorTitle();
	public function disableErrorTitle();
	public function isErrorTitleEnabled();
}
