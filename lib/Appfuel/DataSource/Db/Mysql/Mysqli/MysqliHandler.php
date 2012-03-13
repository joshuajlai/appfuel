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
namespace Appfuel\DataSource\Db\Mysql\Mysqli;

use RunTimeException,
	mysqli as MysqliDriver,
	Appfuel\DataSource\Db\DbResponse,
	Appfuel\DataSource\Db\DbHandlerInterface,
	Appfuel\DataSource\Db\DbRequestInterface,
	Appfuel\DataSource\Db\DbResponseInterface;

/**
 * The database adapter is 
 */
class MysqliHandler implements DbHandlerInterface
{
	/**
	 * @var	mysqli
	 */
	protected $driver = null;

	/**
	 * List of supported adapters that can query ther database server
	 * @var array
	 */
	protected $whitelist = array(
		'QueryAdapter', 
		'MultiQueryAdapter',
		'PreparedStmtAdapter'
	);

	/**
	 * @param	mysqli $driver
	 * @return	AdapterBase
	 */
	public function __construct(MysqliDriver $driver)
	{
		$this->setDriver($driver);
	}

	/**
	 * @param	mysqli	$mysqli
	 * @return	MysqliQueryHandler
	 */
	public function setDriver($mysqli)
	{
        if (! $mysqli instanceof MysqliDriver) {
            $err = 'driver must be a mysqli object';
            throw new InvalidArgumentException($mysqli);
        }

        $this->driver = $mysqli;
		return $this;
	}

	/**
	 * @return	mysqli
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * @return	array
	 */
	public function getSupportedAdapters()
	{
		return $this->whitelist;
	}

	/**
	 * @param	string	$className
	 * @return	bool
	 */
	public function isAdapterSupported($className)
	{
		if (is_string($className) && in_array($className, $this->whitelist)) {
			return true;
		}

		return false;
	}

	/**
	 * @param	DbRequestInterface $request
	 * @return	DbReponseInterface
	 */
	public function execute(DbRequestInterface $request,
							DbResponseInterface $response)
	{
		$className  = ucfirst($request->getType()) . 'Adapter';
		if (! $this->isAdapterSupported($className)) {
			$err  = "can not execute request on the database server because ";
			$err .= "Mysqli adapter -($className) is not support by appfuel";
			throw new RunTimeException($err);
		}

		$class = __NAMESPACE__ . "\\{$className}";
		$adapter = new $class();
		if (! $adapter instanceof MysqliAdapterInterface) {
			$err  = "can not execute -($className) because it does not ";
			$err .= "implment Appfuel\DataSource\Db\DbAdapterInterface";
			throw new LogicException($err);
		}

		return $adapter->execute($this->getDriver(), $request, $response);
	}
}
