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

use Appfuel\Error\ErrorStackInterface,
	Appfuel\DataStructure\DictionaryInterface;

/**
 * This interface is a dictionary used to hold any application specific info
 * that might be assigned before the action controller processing has occured.
 * It is also required to hold the AppInputInterface to the action controllers
 * can retrieve any user input. It is also required to expose functionality of
 * an ErrorStackInterface which allows the action controllers to add one or
 * more error messages and the front controller can process and react to 
 * them according.
 */
interface ContextInterface extends DictionaryInterface
{
	/**
	 * @return	AppInputInterface
	 */
	public function getInput();

	/**
	 * @return	ErrorStackInterface
	 */
	public function getErrorStack();

	/**
	 * @param	ErrorStackInterface		$error
	 * @return	AppContext
	 */
	public function setErrorStack(ErrorStackInterface $error);

	/**
	 * @return	bool
	 */
	public function isError();

	/**
	 * @param	string	$msg
	 * @param	int		$code
	 * @return	AppContext
	 */
	public function addError($msg, $code = 400);

	/**
	 * Use the error stack to produce a string of one or more error messages
	 * @return	string
	 */
	public function getErrorString();
}
