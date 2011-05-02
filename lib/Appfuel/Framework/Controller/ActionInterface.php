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
namespace Appfuel\Framework\Controller;


use Appfuel\Framework\View\ManagerInterface as ViewManagerInterface,
    Appfuel\Framework\MessageInterface;

/**
 * Action controllers are used process the user request manipulate the given
 * document and hand back the message
 */
interface ActionInterface
{
	public function addSupportedDocs(array $types);
	public function addSupportedDoc($type);
	public function getSupportedDocs();
	public function isSupportedDoc($type);
    public function initialize(MessageInterface $msg);
	public function getViewManager();
	public function setViewManager(ViewManagerInterface $vm);
	public function execute(MessageInterface $msg);
}
