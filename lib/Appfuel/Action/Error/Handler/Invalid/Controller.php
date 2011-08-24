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
namespace Appfuel\Action\Error\Handler\Invalid;


use Appfuel\Framework\Exception,
	Appfuel\Action\ActionController,
    Appfuel\Framework\App\ContextInterface;

/**
 * Handle application errors. The front controller uses this controller to 
 * allow error from other controller to be handled here.
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
		$text = 'Error has occured';
		if ($context->isError()) {
			$text = $context->getErrorText();
		}
		
		$this->getViewManager()
			 ->assign('error_text', $text);

	}
}
