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

/**
 * The request uri holds information about the get request from the path
 * onwards. This means just the path and querystring are parsed. From this
 * you must find the route key and seperate all the parameters into an
 * associative array. When the query string is used the route key can use
 * the label 'routekey'. If that label is not found in the query string then
 * you must look for is as the first part of the path and remove it, if
 * there are no parts in the path the route key will be an empty string. When
 * there is no query string (friendly url) then the route key is the first
 * part of the path, all other paremeters must be parsed from name/value to 
 * name=>value. When no part exists the route key is an empty string and the
 * parameters are an empty array.
 */
interface RequestUriInterface
{
	/**
	 * The original uri string that as used.
	 *
	 * @return string
	 */
	public function getUriString();

	/**
	 * Once all the parameters are merged a copy of them will be converted
	 * into a friendly url format and assigned. This allows us to create the
	 * friendly version when the query string is used
	 *
	 * @return string
	 */
	public function getParamString();

	/**
	 * @return string
	 */
	public function getRouteKey();

	/**
	 * Parameters parsed from the path and query string. When both query
	 * string and path exist the query string parames get merged into the
	 * path parameters, like values of the query string will override those 
	 * of the path, thus conficts will throw no exceptions
	 *
	 * @return array
	 */
	public function getParams();
}
