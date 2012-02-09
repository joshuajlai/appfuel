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

use InvalidArgumentException;

/**
 */
class MvcRouteHandler implements MvcRouteHandlerInterface
{
	/**
	 * Master route key for this handler
	 * @var string
	 */
	protected $masterKey = null;

	/**
	 * Master route detail for this handler
	 * @var	MvcRouteDetailInterface
	 */
	protected $masterDetail = null;

	/**
	 * List of route aliases for this detail
	 * @var string
	 */
	protected $aliasMap = array();
	
	/**
	 * @param	array	$data
	 * @return	MvcRouteDetail
	 */
	public function __construct($key, $config = null, array $alias = null)
	{
		$this->loadConfiguration($key, $config, $alias);
	}

	/**
	 * @return	array
	 */
	public function getAliases()
	{
		return array_keys($this->aliasMap);
	}

	/**
	 * @return	string
	 */
	public function getMasterKey()
	{
		return $this->masterKey;
	}

	/**
	 * @return	MvcRouteDetailInterface
	 */
	public function getMasterDetail()
	{
		return $this->masterDetail;
	}

	/**
	 * @param	string $key
	 * @return	bool
	 */
	public function isValidKey($key)
	{
		if (is_string($key) &&
			($key === $this->getMasterKey() || 
			array_key_exists($key, $this->aliasMap))) {
			return true;
		}

		return false;
	}

	/**
	 * @param	string	$key
	 * @return	null | MvcRouteDetailInterface
	 */
	public function getRouteDetail($key)
	{
		if (! is_string($key)) {
			return null;
		}

		$masterKey = $this->getMasterKey();
		if ($key === $masterKey) {
			return $this->getMasterDetail();
		}

		/* make sure the alias exists */
		if (! array_key_exists($key, $this->aliasMap)) {
			return null;
		}

		/* the alias has a full defined route detail of its own */
		$detail = $this->aliasMap[$key];
		if ($detail instanceof MvcRouteDetailInterface) {
			return $detail;
		}

		/* the alias is pointing to the master route detail */
		if ($masterKey === $detail) {
			return $this->getMasterDetail();
		}

		if (! array_key_exists($detail, $this->aliasMap)) {
			return null;
		}

		/* the alias is pointing to the detail of the master route key or 
		 * some other alias
		 */
		$detail = $this->aliasMap[$detail];
		
		/* pointer resolution is shallow only one level so this must be
		 * a route detail object
		 */
		if (! $detail instanceof MvcRouteDetailInterface) {
			return null;
		}
		
		return $detail;
	}
	
	/**
	 * @param	string	$key	main route key
	 * @param	array	$data	
	 * @return	null
	 */
	protected function loadConfiguration($masterKey,
									  array $masterConfig = null, 
									  array $aliases = null)
	{
		$this->setMasterKey($masterKey);
		if (null === $masterConfig) {
			$masterConfig = array();
		}

		/*
		 * merge the default config array so that we can use a completed 
		 * master array without forcing the developer to all items. This 
		 * way we can aliases into master and pick up default values
		 */
		$masterConfig = $this->merge($this->getDefaultConfig(), $masterConfig);
		$this->setMasterDetail($this->createRouteDetail($masterConfig));

		if (empty($aliases)) {
			return $this;
		}

        if ($aliases === array_values($aliases)) {
            $err = "second param must be an associative array -(key=>config)";
			throw new InvalidArgumentException($err);
        }
		
		$map = array();
		foreach ($aliases as $key => $data) {
			if (! is_string($key)) {
				$err = 'alias config invalid: route key must be a string';
				throw new InvalidArgumentException($err);
			}

			if ($masterKey === $key) {
				continue;
			}

			if (is_array($data)) {
				if (empty($data)) {
					$detail = $this->createRouteDetail(array());
				}
				else if (isset($data['is-inherit']) && 
						 false === $data['is-inherit']) {
					$detail = $this->createRouteDetail($data);
				}
				else {
					$config = $this->merge($masterConfig, $data);
					if (null === $config) {
						$err  = "invalid alias config for -($key) could not ";
						$err .= "inherit from master config";
						throw new InvalidArgumentException($err);
					}
					$detail = $this->createRouteDetail($config);
				}
				$map[$key] = $detail;
			}
			else if (is_string($data)) {
				if ($masterKey === $data || array_key_exists($data,$aliases)) {
					$map[$key] = $data;
				}
				else {
					$err  = "alias pointer to other detail failed: ";
					$err .= "-($data) is not an alias or master route key";
					throw new InvalidArgumentException($err);
				}
			}
			else if ($data instanceof MvcRouteDetailInterface) {
				$map[$key] = $data;
			}
			else if (empty($data)) {
				$map[$key] = $masterKey;
			}
			
		}
	
		$this->aliasMap = $map;	
		return $this;
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
	protected function merge(array $master, array $data)
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
					$data[$key] = $this->merge($value, $data[$key]);
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
	 * @param	string	$key
	 * @return	null	
	 */
	protected function setMasterKey($key)
	{
		if (! is_string($key)) {
			$err = 'master route key must be a string';
		}

		$this->masterKey = $key;
	}

	/**
	 * @param	array	$data
	 * @return	null
	 */
	protected function setMasterDetail(MvcRouteDetailInterface $detail)
	{
		$this->masterDetail = $detail;
	}

	/**
	 * @param	array	$data
	 * @return	MvcRouteDetail
	 */
	protected function createRouteDetail(array $data)
	{
		return new MvcRouteDetail($data);
	}

	/**
	 * @return	array
	 */
	protected function getDefaultConfig()
	{
		return array(
			'is-public'		=> false,
			'is-internal'	=> false,
			'acl-access'	=> array(),
			'intercept'		=> array(
				'is-skip-pre'	=> false,
				'include-pre'   => array(),
				'exclude-pre'   => array(),
				'is-skip-post'	=> false,
				'include-post'  => array(),
				'exclude-post'  => array(),
			),
			'view-detail'  => array(
				'is-view'  => true,
				'strategy' => 'general',
				'params'   => array(),
				'method'   => null,
			)
		);
	}
}
