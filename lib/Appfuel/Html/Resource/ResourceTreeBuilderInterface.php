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
namespace Appfuel\Html\Resource;

use DomainException,
	InvalidArgumentException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\Filesystem\FileFinderInterface;

/**
 * Build an intermediate tree (array) which holds information about vendors
 * and their packages
 */
interface ResourceTreeBuilderInterface
{
	/**
	 * @param	string	$path	
	 * @param	bool	$isBasePath
	 * @return	TreeBuilder
	 */
	public function buildTree($path = null, $isBasePath = true);

	/**
	 * @param	FileFinderInterface $finder
	 * @return	ResourceTreeBuilder
	 */
	public function createVendorList(FileFinderInterface $finder);

	/**
	 * @param	array	$vendors
	 * @return	array
	 */
	public function processVendorList(array $vendors);

    /**
     * @param   string  $path
     * @return  array
     */
    public function createPackageTree($path);

    /**
     * @param   string  $vendorFile
     * @return  array
     */
    public function discoverPackageTree($path);

	/**
	 * @return	string
	 */
	public function getDefaultVendorFile();
}
