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
class TestTaskB extends StartupTask
{
	/**
	 * @return	MyStartupTask
	 */
	public function __construct()
	{
		parent::__construct(array('test-b' => null));
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
		$value = $params['test-b'];
		$context->add('test-b', $value);
		$this->execute($params);
	}


	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		$this->setStatus('test-b has executed');	
	}
}
