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
namespace Appfuel\Action\Null;


use Appfuel\Framework\Exception,
	Appfuel\Action\ActionController,
    Appfuel\Framework\App\ContextInterface;

/**
 * The null controller does nothing but act as a pass through. It is useful 
 * for when you need a controller to exist but not do anything.
 */
class Controller extends ActionController
{
	/**
	 * 
	 * @param	ContextInterface $msg
	 * @return	null
	 */
	public function execute(ContextInterface $context)
	{
		return $context;
	}
}
