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
namespace Appfuel\Framework\View;

/**
 * The Composite Template is a file template that holds other view templates.
 */
interface ViewCompositeTemplateInterface extends ViewFileTemplateInterface
{
	/**
	 * @param	scalar	$key
	 * @return	bool
	 */
	public function templateExists($key);
	
	/**
	 * @param	string	$key
	 * @return	ViewTemplateInterface | null when not found
	 */
	public function getTemplate($key);
	
	/**
	 * @param	scalar	$key
	 * @param	ViewTemplateInterface	$template
	 * @return	ViewCompositeTemplateInterface
	 */
	public function addTemplate($key, ViewTemplateInterface $template);
}
