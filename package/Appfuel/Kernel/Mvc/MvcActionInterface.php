<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Mvc;

use Appfuel\Orm\OrmManager;

interface MvcActionInterface
{
	/**
	 * @param	string	$key
	 * @return	OrmRepositoryInterface
	 */
	public function getRepository($key, $source = 'db');

	/**
	 * @param	MvcActionDispatcher
	 * @return	null
	 */
	public function getDispatcher();

	/**
	 * @return 	MvcContextBuilder
	 */
	public function getMvcFactory();

	/**
	 * Must be implemented by concrete class
	 *
	 * @param	AppContextInterface $context
	 * @return	null
	 */
	public function process(MvcContextInterface $context);

	/**
	 * Makes it appear as if the context was used in the action you just 
	 * called. 
	 * 
	 * @param	string	$routeKey
	 * @param	MvcContextInterface $context
	 * @return	MvcContextInterface
	 */
	public function callWithContext($routeKey, MvcContextInterface $context);
}
