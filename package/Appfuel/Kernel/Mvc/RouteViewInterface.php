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

interface RouteViewInterface
{
	/**
	 * @return string
	 */
	public function getFormat();

	/**
	 * @param	string	$name
	 * @return	null
	 */
	public function setFormat($name);

	/**
	 * @return	RouteView
	 */
	public function disableView();

	/**
	 * @return	RouteView
	 */
	public function enableView();

	/**
	 * @return	bool
	 */
	public function isViewDisabled();

	/**
	 * @return	RouteView
	 */
	public function enableManualView();

	/**
	 * @return	RouteView
	 */
	public function disableManualView();

	/**
	 * @return	bool
	 */
	public function isManualView();

	/**
	 * @return	bool
	 */
	public function isViewPackage();

	/**
	 * @return	string
	 */
	public function getViewPackage();

	/**
	 * @param	string	$name
	 * @return	null
	 */
	public function setViewPackage($name);
	
	/**
	 * @return RouteView
	 */
	public function clearViewPackage();
}
