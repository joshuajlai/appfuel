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
namespace Appfuel\Kernal\Dependency;

/**
 * A dependency groups a collection of namespaces or files to be loaded in
 * one load operation by a dependecy loader
 */
interface ClassDependencyInterface
{
	/**
	 * Prefix path used to pre fix to namespace resolution
	 * @return	string
	 */
	public function getRootPath();

	/**
	 * Add a namespace to be resolved and required
	 * @param	string	
	 * @return	ClassDependencyInterface
	 */
	public function addNamespace($ns);

	/**
	 * Add a list of namespaces to be resolved
	 * @param	array	$ns
	 * @return	ClassDependencyInterface
	 */
	public function loadNamespaces(array $ns);

	/**
	 * @return	array
	 */
	public function getNamespaces();

	/**
	 * @return	array
	 */
	public function getFiles();

	/**
	 * @param	string	$file
	 * @return	ClassDependencyInterface
	 */
	public function addFile($file);
	
	/**
	 * @param	array	$files
	 * @return	ClassDependencyInterface
	 */
	public function loadFiles(array $files);
}
