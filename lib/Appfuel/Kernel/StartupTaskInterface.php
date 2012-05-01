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

use Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

/**
 * A strategy pattern used to encapsulates any logic needed to configure or 
 * initialize something. The Task acts on data injected into its execute or
 * kernelExecute methods. A task does not get any of its  data, instead it 
 * describes what it wants with set/getRegistryKeys. Registry keys are an
 * associative array of key => defaultValue. The task handler uses this
 * to collect that data out of the Config Registry.
 */
interface  StartupTaskInterface
{
	/**
	 * Reports the result of the initialization
	 *
	 * @return	string
	 */
	public function getStatus();

	/**
	 * List of keys to pull out of the registry
	 *
	 * @return	null|string|array
	 */
	public function getRegistryKeys();

    /**
     * @param   array   $params
     * @param   MvcRouteDetailInterface $route
     * @param   MvcContextInterface $context
     * @return  null
     */
    public function kernelExecute(array $params = null,
                                  MvcRouteDetailInterface $route,
                                  MvcContextInterface $context);

	/**
	 * @param	array	$params	
	 * @return	null
	 */
	public function execute(array $params = null);
}
