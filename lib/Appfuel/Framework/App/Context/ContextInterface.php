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
namespace Appfuel\Framework\App\Context;


use Appfuel\Framework\App\Route\RouteInterface,
	Appfuel\Framework\Domain\User\UserInterface,
	Appfuel\Framework\App\Request\RequestInterface,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * The context is an object that contains all the necessary information for
 * the mvc system to execute an action on a controller pointed to by a route
 * for a system or user.
 */
interface ContextInterface extends DictionaryInterface
{
	/**
	 * The operation the user requested based on the routestring 
	 * @return	Appfuel\Framework\Domain\Operation\OperationInterface
	 */
	public function getOperation();

	/**
	 * The object that olds all the user input. Http GET and POST aswell
	 * as the commandline's argv are found in this object
	 * @return Appfuel\Framework\App\Request\RequestInterface
	 */
	public function getRequest();

    /**
     * User requesting the execution of the current operation
     *
     * @return  Appfuel\Framework\Domain\User\UserInterface
     */
    public function getCurrentUser();

    /**
	 * No assumptions are made about the user. Its the developer's reposibility
	 * to validate if the object given adheres to your notion of a user.
	 *
     * @param   UserInterface
     * @return  null
     */
    public function setCurrentUser($user);

    /**
     * Flag used to determine if the current user was set
     *
     * @return  bool
     */
    public function isCurrentUser();

	/**
	 * @return	Appfuel\Framework\Exception
	 */
	public function getException();
	
	/**
	 * @param	string		$text
	 * @param	mixed		$code
	 * @param	\Exception	$prev	previous exception that might have occured
	 * @return	ContextInterface
	 */	
	public function setException($text, $code = 0, \Exception $prev = null);
	
	/**
	 * @return	bool
	 */
	public function isException();
	
	/**
	 * @return	ContextInterface
	 */
	public function clearException();
}
