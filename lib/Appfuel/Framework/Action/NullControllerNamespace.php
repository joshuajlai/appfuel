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
namespace Appfuel\Framework\Action;


use Appfuel\Framework\Exception;

/**
 * Null object pattern for the controller namespace
 */
class NullControllerNamespace extends ControllerNamespace 
{
	/**
	 * @param	string	$actionNs	full namespace of the action controller
	 * @return	ActionControllerDetail
	 */
	public function __construct()
	{
        parent::__construct('Appfuel\Action\Null\SubModule\Action');
	}
}
