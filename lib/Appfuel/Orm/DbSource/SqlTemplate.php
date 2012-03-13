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

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\PathFinder,
	Appfuel\Kernel\PathFinderInterface,
	Appfuel\View\FileViewTemplate;

/**
 */
class SqlTemplate extends FileViewTemplate
{
	/**
	 * @param	mixed	$file 
	 * @param	DbMapInterface $map
	 * @param	PathFinderInterface	$pathFinder
	 * @return	FileViewTemplate
	 */
	public function __construct($file, 
								DbMapInterface $dbMap = null,
								PathFinderInterface $pathFinder = null)
	{
		if (null === $pathFinder) {
			$pathFinder = new PathFinder(self::getResourceDir());
		}
		if (null !== $dbMap) {
			$this->setDbMap($dbMap);
		}
		$this->setFile($file);
		$this->setViewCompositor(new SqlFileCompositor($pathFinder));
	}

	/**
	 * @return	DbMapInterface
	 */
	public function getDbMap()
	{
		return $this->dbMap;
	}

    /**
     * Build the template file indicated by key into string. Use data in
     * the dictionary as scope
     *
     * @param   string  $key    template file identifier
     * @param   array   $data   used for private scope
     * @return  string
     */
    public function build()
    {   
        $compositor = $this->getViewCompositor();
        if (! ($compositor instanceof SqlFileCompositorInterface)) {
            $err  = 'build failed: when a template file is set the view ';
            $err .= 'compositor must implement Appfuel\Orm\DbSource';
            $err .= '\SqlFileCompositorInterface';
            throw new RunTimeException($err);
        }

        if ($this->templateCount() > 0) {
            $this->buildTemplates();
        }

        $compositor->setFile($this->getFile());
		$compositor->setDbMap($this->getDbMap());

        return $compositor->compose($this->getAll());
    }

	/**
	 * @param 	DbMapInterface	$map
	 * @return	null
	 */
	protected function setDbMap(DbMapInterface $map)
	{
		$this->dbMap = $map;
		return $this;
	}
}
