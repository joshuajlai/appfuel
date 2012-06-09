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

use LogicException,
	InvalidArgumentException;

class RouteBuilder
{
	/**
	 * @var array
	 */
	static protected $default =  array(
		'is-public'		=> false,
		'is-internal'	=> false,
		'startup'		=> array(
			'is-ignore-config' => false,
			'is-prepend'	   => false,
			'include'		   => array(),
			'exclude'		   => array(),
		),
		'intercept'	=> array(
			'is-skip-pre'	=> false,
			'include-pre'   => array(),
			'exclude-pre'   => array(),
			'is-skip-post'	=> false,
			'include-post'  => array(),
			'exclude-post'  => array(),
		),
		'is-manual-view'   => false,
		'view-pkg'		   => null,
	);

	/**
	 * @return	array
	 */
	static public function getDefaultConfiguration()
	{
		return self::$default;
	}
		
	/**
	 * @param	string	$key	main route key
	 * @param	array	$data	
	 * @return	null
	 */
	static public function buildRoutes(array $routes)
	{
		if ($routes === array_values($routes)) {
            $err = "route list must be an associative array -(key=>config)";
			throw new InvalidArgumentException($err);
        }

		/*
		 * The first route is consider the main route which all others will
		 * inherit from
		 */ 
		$keys    = array_keys($routes);
		$mainKey = array_shift($keys);
		$main    = array_shift($routes);
		
		/*
		 * merge the default config array so that we can use a completed 
		 * master array without forcing the developer to all items. This 
		 * way we can aliases into master and pick up default values
		 */
		$default    = self::getDefaultConfiguration();
		$main       = self::merge($default, $main);
		$mainRoute  = self::createRouteDetail($main);

		$list = array($mainKey => $mainRoute);
		if (empty($routes)) {
			return $list;
		}

		foreach ($routes as $key => $data) {
			if (! is_string($key)) {
				$err = 'alias config invalid: route key must be a string';
				throw new InvalidArgumentException($err);
			}

			if ($mainKey === $key) {
				continue;
			}

			if (is_array($data)) {
				if (empty($data)) {
					$detail = $self::createRouteDetail($data);
				}
				else if (isset($data['is-inherit']) && 
						 false === $data['is-inherit']) {
					$detail = self::createRouteDetail($data);
				}
				else {
					$config = self::merge($main, $data);
					if (null === $config) {
						$err  = "invalid alias config for -($key) could not ";
						$err .= "inherit from master config";
						throw new InvalidArgumentException($err);
					}
					$detail = self::createRouteDetail($config);
				}
				$list[$key] = $detail;
			}
			else if (is_string($data)) {
				if ($mainKey === $data || in_array($data,$keys, true)) {
					$list[$key] = $data;
				}
				else {
					$err  = "alias pointer to other detail failed: ";
					$err .= "-($data) is not an alias or master route key";
					throw new InvalidArgumentException($err);
				}
			}
			else if ($data instanceof MvcRouteDetailInterface) {
				$list[$key] = $data;
			}
			else if (empty($data)) {
				$list[$key] = $mainKey;
			}
			
		}
	
		return $list;	
	}

	/**
	 * php does not merge indexed arrays the way we need, values with the 
	 * same index keys are overwritten when we want all unique values for 
	 * both arrays. I wrote this merge (quickly not the best) to meet this
	 * need
	 *
	 * @param	array	$master
	 * @param	array	$data
	 * @return	array
	 */
	static protected function merge(array $master, array $data)
	{
		$result = array();
		foreach ($master as $key => $value) {
			if (! array_key_exists($key, $data)) {
				$data[$key] = $value;
				continue;
			}

			/* override the master value */		
			if (! is_array($data[$key])) {
				continue;
			}

			/* both values are arrays so merge them */
			if (is_array($value) && is_array($data[$key])) {

				/* merge the unique values togather for indexed arrays*/
				if ($value === array_values($value)) {
					foreach ($value as $item) {
						$data[$key][] = $item;
					}
					$data[$key] = array_unique($data[$key]);
				}
				/* recurse through assosiative arrays */
				else {
					$data[$key] = self::merge($value, $data[$key]);
				}
			}
			else if (is_array($value) && is_scalar($data[$key])) {
					$tmp = $value;
					$tmp[] = $data[$key];
					$data[$key] = $tmp;
			}
		}

		return $data;
	}

	/**
	 * @param	array	$data
	 * @return	MvcRouteDetail
	 */
	static public function createRouteDetail(array $data)
	{
		return new MvcRouteDetail($data);
	}

}
