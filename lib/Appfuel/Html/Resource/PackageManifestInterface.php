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
namespace Appfuel\Html\Resource;

/**
 * A value object used to describe the manifest.json in the package directory
 */
interface PackageManifestInterface
{
	/**
	 * @return	string
	 */
	public function getPackageName();

	/**
	 * @return	string
	 */
	public function getPackageDescription();

	/**
	 * @return	string
	 */
	public function getSourceDirectory();

	/**
	 * @return	array
	 */
	public function getSourceTypes();

	/**
	 * @return	string
	 */
	public function getAllSourceFiles();

	/**
	 * @params	string $type 
	 * @return	array|false
	 */
	public function getSourceFiles($type);

	/**
	 * @return	array
	 */
	public function getSourceDependencies();

	/**
	 * @return	string
	 */
	public function getTestDirectory();

	/**
	 * @return	array
	 */
	public function getTestFileTypes();

	/**
	 * @return	array
	 */
	public function getAllTestFiles();

	/**
	 * @param	string	$type
	 * @return	array|false
	 */
	public function getTestFiles($type);

	/**
	 * @return	array
	 */
	public function getTestDependencies();
}
