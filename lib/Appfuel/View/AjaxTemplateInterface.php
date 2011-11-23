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
namespace Appfuel\View;

/**
 * Interface needed to service ajax calls from the application.
 */
interface AjaxTemplateInterface extends ViewTemplateInterface
{
	/**
	 * @return	string
	 */
	public function getStatusCode();
	
	/**
	 * @param	scalar	$code
	 * @return	JsonTemplateInterface
	 */
	public function setStatusCode($code);
	
	/**
	 * @return	string
	 */
	public function getStatusText();
	
	/**
	 * @param	string	$text
	 * @return	JsonTemplateInterface
	 */
	public function setStatusText($text);
	
	/**
	 * @param	scalar	$code
	 * @param	string	$text
	 * @return	JsonTemplateInterface
	 */
	public function setStatus($code, $text);
}
