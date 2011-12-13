<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Db;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\Startup\StartupTaskAbstract;

/**
 *  
 */
class DbStartup extends StartupTaskAbstract 
{
	/**
	 * Assign the registry keys to be pulled from the kernel registry
	 * 
	 * @return	KernelIntializeTask
	 */
	public function __construct()
	{
		$this->setRegistryKeys(array('db'));
	}

	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		$err = 'Could not startup the database: ';
		if (! isset($params['db']) || 
			empty($params['db']) ||
			! is_array($params['db'])) {
			$err .= 'no config entry for "db"';
			throw new RunTimeException($err);
		}
		$db = $params['db'];

		if (! isset($db['connectors']) ||
			empty($db['connectors']) ||
			! is_array($db['connectors'])) {
			$err .= 'Could not find connectors or connector entry is empty';
			throw new RunTimeException($err);
		}
		$connectors = $db['connectors'];
		$max = count($connectors);
		foreach ($connectors as $key => $data) {
			$connector = $this->buildConnector($data);
			DbRegistry::addConnector($key, $connector);
		}

		$this->setStatus("DbRegistry intialized with $max connectors");
	}

	/**
	 * @throws	RunTimeException
	 * @param	array	$data
	 * @return	DbConnectorInterface
	 */
	protected function buildConnector(array $data)
	{
		$err = 'Could not build connector: ';
		if (! isset($data['master'])) {
			if (! isset($data['class']) || ! is_string($data['class'])) {
				$err .= 'connector class not found key used is -(class)';
				throw new RunTimeException($err);
			}
			$class = $data['class'];
			$connection = new $class(new ConnectionDetail($data));
			$connector = new DbConnector($connection);
			return $connector;
		}

		$master = $data['master'];
		if (empty($master) || ! is_array($master)) {
			$err .= 'master has been defined but is empty or not an array';
			throw new RunTimeException($err);
		}
		
		if (! isset($master['class']) || ! is_string($master['class'])) {
			$err .= 'master connector class not found. key used is -(class)';
			throw new RunTimeException($err);
		}
		$class = $master['class'];
		$mconn = new $class(new ConnectionDetail($master));

		$sconn = null;
		if (isset($data['slave']) && is_array($data['slave'])) {
			$slave = $data['slave'];
			if (! isset($slave['class']) || ! is_string($slave['class'])) {
				$err .= 'slave connector class not found. key used is -(class)';
				throw new RunTimeException($err);
			}
			$class = $slave['class'];	
			$sconn = new $class(new ConnectionDetail($slave));
		}

		return new DbConnector($mconn, $sconn);
	}
}	
