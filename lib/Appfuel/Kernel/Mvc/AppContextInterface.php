<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use Appfuel\View\ViewTemplateInterface,
	Appfuel\DataStructure\DictionaryInterface;

/**
 * This interface is a dictionary used to hold any application specific info
 * that might be assigned before the action controller processing has occured.
 * It is also required to hold the AppInputInterface to the action controllers
 * can retrieve any user input.
 */
interface AppContextInterface extends DictionaryInterface
{
	/**
	 * @return	string
	 */
	public function getStrategy();

	/**
	 * @return	string
	 */
	public function getRoute();

	/**
	 * List of codes used for role based access control
	 * @return	array
	 */
	public function getAclCodes();

	/**
	 * @param	string	$code
	 * @return	ContextInterface
	 */
	public function addAclCode($code);

	/**
	 * @param	string	$code
	 * @return	bool
	 */
	public function isAclCode($code);

	/**
	 * @return	AppInputInterface
	 */
	public function getInput();

	/**
	 * @return	ViewTemplateInterface
	 */
	public function getView();
	
	/**
	 * @param	ViewTemplateInterface
	 * @return	AppContextInterface
	 */
	public function setView(ViewTemplateInterface $view);

	/**
	 * @return	int
	 */
	public function getExitCode();
	
	/**
	 * @param	int $code
	 * @return	AppContextInterface
	 */
	public function setExitCode($code);
}
