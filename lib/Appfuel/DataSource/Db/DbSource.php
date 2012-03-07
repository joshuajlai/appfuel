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

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\View\FileViewTemplate;

/**
 * The database handler is responsible for handling database requests without
 * knowledge of the database vendor or what adapter is used with that vendor.
 * Its only job is to grab the correct connection, use it to create an 
 * adaoter  and decide which adapter method to use based on the request, 
 * finishing by returning a DbResponse.
 */
class DbSource implements DbSourceInterface
{
    /**
     * List of sql template files used by the datasource
     * @var array
     */
    protected $sqlFiles = array();

	/**
	 * @param	array	$paths
	 * @return	DbSource
	 */
	public function __construct(array $paths = null)
	{
		if (null !== $paths) {
			$this->loadSqlTemplatePaths($paths);
		}
	}

    /**
     * @param   array   $list
     * @return  DSource
     */
    public function loadSqlTemplatePaths(array $list)
    {
        foreach ($list as $key => $path) {
            $this->addSqlTemplatePath($key, $path);
        }

        return $this;
    }

    /**
     * @param   string  $name
     * @return  string | false when not found
     */
    public function getSqlTemplatePath($name)
    {
        if (! is_string($name) || ! isset($this->sqlFiles[$name])) {
            return false;
        }

        return $this->sqlFiles[$name];
    }

    /**
     * @param   string  $name   used to file the template path
     * @param   stirng  $path   path to sql template
     * @return  DbSource
     */
    public function addSqlTemplatePath($key, $path)
    {
        if (! is_string($key) || empty($key)) {
            $err = 'sql template key must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        if (! is_string($path) || empty($path)) {
            $err = 'sql template path must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        $this->sqlFiles[$key] = $path;
        return $this;
    }

    /**
     * @param   string  $file
     * @param   array   $data
     * @return  string
     */
    public function loadSql($key, array $data = null, $returnTemplate = false)
    {
        $path = $this->getSqlTemplatePath($key);
        if (! is_string($path) || empty($path)) {
            $err = "could not load sql template path found with key -($key)";
            throw new RunTimeException($err);
        }

        $template = $this->createSqlTemplate($path);
        if (null !== $data) {
            $template->load($data);
        }

        if (true === $returnTemplate) {
            return $template;
        }

        return $template->build();
    }

    /**
     * @param   string  $file
     * @return  FileViewTemplate
     */
    public function createSqlTemplate($file)
    {
        return new FileViewTemplate($file);
    }

	/**
	 * Use the connection to create an adapter that will service the request.
	 * Every request has a code that the connection object uses to determine
	 * which adapter will be used. 
	 *
	 * @param	DbRequestInterface $request
	 * @param	DbResponseInterface $response
	 * @param	string	$key 
	 * @return	DbResponse
	 */
	public function execute(DbRequestInterface $request,
							DbResponseInterface $response = null,
							$key = null)
	{
		$connector = $this->getConnector($key);
		if (! $connector instanceof DbConnectorInterface) {
			$err  = "Database startup task has not been run or your ";
			$err .= "configuration has no database connectors, could not ";
			$err .= "find database connector for -($key)";
			throw new LogicException($err);
		}

		if (null === $response) {
			$response = $this->createResponse();
		}

		$conn = $connector->getConnection($request->getStrategy());
		if (! $conn instanceof DbConnInterface) {
			$err  = 'Database connector has not been correctly instatiated ';
			$err .= 'connection object must implment an Appfuel\DataSource';
			$err .= '\Db\DbConnInterface';
			throw new LogicException($err);
		}

		if (! $conn->isConnected()) {
			$conn->connect();
		}

		$handler = $conn->createDbAdapter($request->getType());
		if (! $handler instanceof DbHandlerInterface) {
			$class = get_class($handler);
			$err   = "database vendor adapter -($class) does not implement ";
			$err  .= "Appfuel\DataSource\Db\DbAdapterInterface";
			throw new LogicException($err);
		}

		return $handler->execute($request, $response);
	}

	/**
	 * @param	mixed $connector
	 * @return	bool
	 */
	public function createResponse()
	{
		return new DbResponse();
	}

	public function createRequest($sql, $type = null, $strategy = null)
	{
		return new DbRequest($sql, $type, $strategy);
	}

	/**
	 * @param	string	$key
	 * @return	DbConnectorInterface | false 
	 */
	public function getConnector($key = null)
	{
		if (null === $key) {
			$key = DbRegistry::getDefaultConnectorKey();
		}
		
		return DbRegistry::getConnector($key);
	}
}
