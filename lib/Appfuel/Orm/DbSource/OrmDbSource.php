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
	Appfuel\DataSource\Db\DbSource;

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
        return new SqlTemplate($file, $this->getDbMap());
    }
}
