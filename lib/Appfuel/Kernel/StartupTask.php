<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel;

use LogicException,
	DomainException,
	InvalidArgumentException,
	Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

/**
 * A strategy pattern used to encapsulates any logic needed to configure or 
 * initialize something. The Task acts on data injected into its execute or
 * kernelExecute methods. A task does not get any of its  data, instead it 
 * describes what it wants with set/getRegistryKeys. Registry keys are an
 * associative array of key => defaultValue. The task handler uses this
 * to collect that data out of the Config Registry.
 */
class StartupTask implements StartupTaskInterface
{
	/**
	 * List of keys to be collected by the registry the keys are structured
	 * as label => default value.
	 * @var	 array
	 */
	protected $keys = array();

	/**
	 * Status text used to indicate the result of task initialization
	 * @var string
	 */
	protected $status = null;

	/**
	 * @param	array	$keys
	 * @return	StartupTask
	 */
	public function __construct(array $keys = null)
	{
		if (null !== $keys) {
			$this->loadRegistryKeys($keys);
		}
	}

	/**
	 * Reports the result of the initialization
	 *
	 * @return	string
	 */
	public function getStatus()
	{
		return $this->status;		
	}

	/**
	 * List of keys to pull out of the registry
	 *
	 * @return	null|string|array
	 */
	public function getRegistryKeys()
	{
		return $this->keys;
	}

	/**
	 * @return	StartupTask
	 */
	public function clearRegistryKeys()
	{
		$this->keys = array();
		return $this;
	}

	/**
	 * @param	array	$keys
	 * @return	StartupTask
	 */
	public function loadRegistryKeys(array $keys)
	{
		/* non associative arrays use the default as null */
		if ($keys === array_values($keys)) {
				foreach ($keys as $key) {
					$this->addRegistryKey($key);
				}
		}
		else {
			foreach ($keys as $key => $default) {
				$this->addRegistryKey($key, $default);
			}
		}
		return $this;
	}

	/**
	 * @param	array	$keys
	 * @return	null
	 */
	public function setRegistryKeys(array $keys)
	{
		$this->clearRegistryKeys();
		$this->loadRegistryKeys($keys);
		return $this;
	}

	/**
	 * @param	string	$label	
	 * @param	mixed	$default
	 * @return	null
	 */
	public function addRegistryKey($label, $default = null)
	{
		if (! is_string($label) || empty($label)) {
			$err = 'label must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->keys[$label] = $default;
		return $this;
	}

    /**
     * @param   array   $params
     * @param   MvcRouteDetailInterface $route
     * @param   MvcContextInterface $context
     * @return  null
     */
    public function kernelExecute(array $params = null,
                                  MvcRouteDetailInterface $route,
                                  MvcContextInterface $context)
    {
        return $this->execute($params);
    }

	/**
	 * @param	array	$params	
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		throw new LogicException("execute method must be extended");
	}

	/**
	 * We don't type hint array on params in order to provide a more readable
	 * excecption
	 *
	 * @throws	DomainException
	 * @param	array	$params		
	 * @param	string	$msg	prepends to error msg
	 * @return	true
	 */
	protected function validateRegistryKeys($params, $msg = '')
	{
		if (! is_string($msg)) {
			$msg = '';		
		}

		if (! is_array($params) || empty($params)) {
			$err = "$msg expecting an array of params: none given";
			throw new DomainException(trim($err));			
		}

		 $key = $this->checkKeys($this->getRegistryKeys(), $params);
		if (true !== $key) {
			$err = "$msg key -($key) is required but not found";
			throw new DomainException(trim($err));
		}

		return true;
	}

	/**
	 * @param	array	$keys
	 * @param	array	$data
	 * @return	bool
	 */
	protected function checkKeys(array $keys, array $data)
	{
		foreach ($keys as $key) {
			if (! array_key_exists($key, $data)) {
				return $key;
			}
		}

		return true;
	}

	/**
	 * @param	string	$text
	 * @return	null
	 */
	protected function setStatus($text)
	{
		if (! is_string($text)) {
			throw new InvalidArgumentException("status must be a string");
		}

		$this->status = trim($text);
	}
}
