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
namespace Appfuel\App\Resource;

use InvalidArgumentException,
	Appfuel\Kernel\PathFinder,
	Appfuel\Kernel\PathFinderInterface;

/**
 */
class DependencyResolver implements DependencyResolverInterface
{
	/**
	 * Datastructure used to hold dependency relationships
	 * @var array
	 */
	protected $graph = array();

	/**
	 * @param	string	$file	relative path to the dependency file
	 * @param	PathFinder $finder	resolves relative paths to absolute
	 * @return	DependencyResolver
	 */
	public function __construct(array $data)
	{
		$this->graph = $data;
	}

	/**
	 * @return	array
	 */
	public function getDependencyGraph()
	{
		return $this->graph;
	}

	public function resolve(array $modules, PackageInterface $package)
	{
		
	}
}
