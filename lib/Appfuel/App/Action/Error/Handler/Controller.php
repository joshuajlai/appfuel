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
namespace Appfuel\App\Action;


use Appfuel\Framework\Exception,
    Appfuel\Framework\MessageInterface,
	Appfuel\App\Action\Controller as ActionController;

/**
 * Handle application errors. The front controller uses this controller to 
 * allow error from other controller to be handled here.
 */
class Controller extends ActionController
{

	/**
	 * Declare supported documents html, json and cli
	 * 
	 * @return	Controller
	 */
	public function __construct()
	{
		$this->addSupportedDocs(array('html', 'json', 'cli'))
			 ->disableInputScheme();
	}

	/**
	 * 
	 * @param	MessageInterface $msg
	 * @param	MessageInterface
	 */
	public function execute(MessageInterface $msg)
	{
		$text = 'Error has occured';
		if ($msg->isError()) {
			$text = $msg->getErrorText();
		}
		
		$this->getViewManager();
			 ->assign('error_text', $text);

		return $msg;
	}
}
