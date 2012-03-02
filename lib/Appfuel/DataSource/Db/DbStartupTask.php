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
namespace Appfuel\DataSource\Db;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\Startup\StartupTaskAbstract;

/**
 *  
 */
class DbStartupTask extends StartupTaskAbstract 
{
	/**
	 * @var DbFactoryInterface
	 */
	protected $factory = null;

	/**
	 * Assign the registry keys to be pulled from the kernel registry
	 * 
	 * @return	KernelIntializeTask
	 */
	public function __construct(DbFactoryInterface $factory = null)
	{
		if (null === $factory) {
			$factory = new DbFactory();
		}
		$this->setFactory($factory);
		$this->setRegistryKeys(array('db', 'db-scope'));
	}

	/**
	 * @return	DbFactoryInterface
	 */
	public function getFactory()
	{
		return $this->factory;
	}

	/**
	 * @param	DbFactoryInterface
	 * @return	DbStartupTask
	 */
	public function setFactory(DbFactoryInterface $factory)
	{
		$this->factory = $factory;
		return $this;
	}

	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		if (empty($params)) {
			$err = 'database startup requires config params which are empty';
			throw new InvalidArgumentException($err);
		}

		if (! isset($params['db']) || ! is_array($params['db'])) {
			$err = 'database config is not set or not an array';
			throw new InvalidArgumentException($err);
		}

		$db = $params['db'];
		if (! is_array($db) || $db === array_values($db)) {
			$err  = 'database config must be an associative array of ';
			$err .= 'connector-key=>connector-config pairs';
			throw new InvalidArgumentException($err);
		}
		
		/*
		 * used when many database connectors are defined in the
		 * common section of a config but you want to confine your app to
		 * a subset of those connectors
		 */
		if (isset($params['db-scope'])) {
			$scope = $params['db-scope'];
			if (is_string($scope) && isset($db[$scope])) {
				$db = array($scope => $db[$scope]);
			}
			else if (is_array($scope)) {
				$tmp = array();
				foreach ($db as $key => $data) {
					if (in_array($key, $scope)) {
						$tmp[$key] = $data;
					}
				}
				$db = $tmp;
				unset($key);
				unset($tmp);
				unset($data);
			}
		}

		/**
		 * For every connector we need to determine the adapter which 
		 * is a vendor specific connection class, and if that connector
		 * is using replication and finally if their is a custom connector
		 * class to be used. With that we create a series of connectors and
		 * add them to the db registry to be used
		 */
		$factory = $this->getFactory();
		foreach ($db as $key => $config) {
			if (! is_string($key) || empty($key)) {
				$err = 'connector key must be a non empty string';
				throw new InvalidArgumentException($err);
			}
				
			if (! isset($config['adapter'])) {
				$err = 'missing config setting -(adapter)';
				throw new InvalidArgumentException($err);
			}
		
			/* save the connection parameters separately in the registry
			 * so that connections setting can be reused without having to 
			 * go back to the config file
			 */
			DbRegistry::addConnectionParams($key, $config);
		


			$slave = null;
			$adapterClass   = $config['adapter'];
			$connectorClass = null;
			if (isset($config['connector-class'])) {
				$connectorClass = $config['connector'];
				if (! is_string($connectorClass) || empty($connectorClass)) {
					$err = 'connector class must be a non empty string';
					throw new InvalidArgumentException($err);
				}
			}

			if (isset($config['master-params'])) {
				$master  = $factory->createConnection(
					$adapterClass, 
					$config['master-params']
				);

				if (isset($config['slave-params'])) {
					$slave = $factory->createConnection(
						$adapterClass,
						$config['slave-params']
					);
				} 			
			}
			else if (isset($config['conn-params'])) {
				$master = $factory->createConnection(
					$adapterClass,
					$config['conn-params']
				);
			}
			else {
				$err  = 'could not find any connection parameters, looking ';
				$err .= 'for: replication -(master-params|[slave-params]) or ';
				$err .= 'non-replication -(conn-params)';
				throw new InvalidArgumentException($err);
			}

			/* when connector class is null the factory will create a default
			 * connector, for appfuel that is DbConnector
			 */
			$connector = $factory->createConnector(
				$master,
				$slave, 
				$connectorClass
			);

			/*
			 * The framework relies on the fact that we build and add db 
			 * connectors to the registry. Datasources have no idea how to 
			 * create connectors. 
			 */
			DbRegistry::addConnector($key, $connector);
		}

	}
}
