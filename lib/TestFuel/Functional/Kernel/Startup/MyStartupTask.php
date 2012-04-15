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

use Appfuel\Kernel\Startup\StartupTask;

/**
 * Used to test the task handler
 */
class MyStartupTask extends StartupTask
{
	/**
	 * @return	MyStartupTask
	 */
	public function __construct()
	{
		parent::__construct(array('foo' => null));
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
		$context->add('my-startup-task', 'foobar');
		$this->execute($params);
	}


	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		$this->setStatus('my startup has executed');	
	}
}
