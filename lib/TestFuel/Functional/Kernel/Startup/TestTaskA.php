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
namespace TestFuel\Functional\Kernel\Startup;

use Appfuel\Kernel\Startup\StartupTask,
	Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

/**
 * Used to test the task handler
 */
class TestTaskA extends StartupTask
{
	/**
	 * @return	MyStartupTask
	 */
	public function __construct()
	{
		parent::__construct(array('test-a' => null));
	}

	/**
	 * @param	MvcRotueDetailInterface $route
	 * @param	MvcContextInterface $context
	 * @return	null
	 */
	public function kernelExecute(array $params = null,
								  MvcRouteDetailInterface $route,
								  MvcContextInterface $context)
	{
		$context->add('test-a', 'value-a');
		$this->execute($params);
	}


	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		$this->setStatus('test-a has executed');	
	}
}
