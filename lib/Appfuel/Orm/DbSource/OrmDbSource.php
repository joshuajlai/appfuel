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
namespace Appfuel\Orm\DbSource;

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\View\FileViewTemplate,
	Appfuel\Orm\OrmCriteriaInterface,
	Appfuel\DataSource\Db\DbSource,
	Appfuel\DataSource\Db\DbResponseInterface;

/**
 */
class OrmDbSource extends DbSource implements OrmDbSourceInterface
{
	/**
	 * List of table maps to be injected into the sql template
	 * @var DbMapInterface
	 */
	protected $map = null;

    /**
     * List of sql template files used by the datasource
     * @var array
     */
    protected $sqlFiles = array();

	/**
	 * @param	array	$paths
	 * @return	DbSource
	 */
	public function __construct(array $paths = null, 
								DbMapInterface $map = null)
	{
		if (null !== $paths) {
			$this->loadSqlTemplatePaths($paths);
		}

		if (null !== $map) {
			$this->setDbMap($map);
		}
	}

    /**
     * @param   array   $list 
     * @return  SqlCompositor
     */
    public function setDbMap(DbMapInterface $map)
    {
		$this->map = $map;
        return $this;
    }

    /**
     * @return  SqlFileCompositor
     */
    public function getDbMap()
    {
        return $this->map;
    }

	/**
	 * @return	bool
	 */
	public function isDbMap()
	{
		return $this->map instanceof DbMapInterface;
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
    public function loadSql($key, 
							OrmCriteriaInterface $criteria = null, 
							$isBuild = true)
    {
        $path = $this->getSqlTemplatePath($key);
        if (! is_string($path) || empty($path)) {
            $err = "could not load sql template path found with key -($key)";
            throw new RunTimeException($err);
        }

        $template = $this->createSqlTemplate($path);
        if (null !== $criteria) {
            $template->assign('domain-criteria', $criteria);
        }

        if (false === $isBuild) {
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
        return new SqlTemplate($file, $this->getDbMap());
    }

	public function process($key, 
							$type = 'Query', 
							$strategy = 'read', 
							$values = null, 
							$callback = null, 
							OrmCriteriaInterface $criteria = null)
	{
		if (null === $type) {
			$type = 'Query';
		}

		if (null === $strategy) {
			$strategy = 'read';
		}

		$sql = $this->loadSql($key, $criteria); 
		$request = $this->createRequest($sql, $type, $strategy, $values);

		if (null !== $callback) {
			if (! is_callable($callback) && is_string($callback)) {
				$callback = $this->createMapperCallback($callback);				
			}
			else if (! is_callable($callback)) {
				$err  = 'callback can only be a string which is the name of ';
				$err .= 'the table map key something that is callable';
				throw new InvalidArgumentException($err);
			}

			$request->setCallback($callback);
		}

		return $this->getResultset($this->execute($request));
		
	}

	/**
	 * @param	DbTableMapInterface $map
	 * @return	Closure
	 */
	public function createMapperCallback($key)
	{
		if (! is_string($key)) {
			$err = 'table map key must be a non empty string';
			throw new InvalidArgumentException($key);
		}

		if (! $this->isDbMap()) {
			$err = 'can not create mapper callback when db map is not set';
			throw new RunTimeException($err);
		}
		$dbMap = $this->getDbMap();

		if (! $dbMap->isTableMap($key)) {
			$err  = "could not create mapper callback table map not found ";
			$err .= "-($key)";
			throw new RunTimeException($er);
		}

		$map = $dbMap->getTableMap($key);
        $callback = function($row) use($map) {
            $result = array();
            foreach ($row as $column => $value) {
                $member = $map->mapMember($column);
                $result[$member] = $value;
            }
            return $result;
        };

		return $callback;
	}

	/**
	 * @throws	RunTimeException
	 * @param	DbResponseInterface $response
	 * @return	array
	 */
	public function getResultset(DbResponseInterface $response)
	{
        if ($response->isError()) {
            $stack = $response->getErrorStack();
			$msg = 'DbSource Error has occured: ';
			foreach ($stack as $error) {
				$msg .= "{$error->getMessage()} [{$error->getCode()}]: ";
			}
            throw new RunTimeException($msg, 500);
        }

        return $response->getResultSet();
	}
}
