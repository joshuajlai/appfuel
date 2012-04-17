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
namespace Appfuel\View;

use DomainException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileFinderInterface;

/**
 */
interface ViewCompositorInterface
{
	/**
	 * @param	string	$file	
	 * @param	string	$data
	 * @return	string
	 */
	static public function composeFile($file, array $data);

	/**
	 * @param	array	$data	
	 * @param	int		$options
	 * @return	string
	 */
	static public function composeJson(array $data, $options = 0);

	/**
	 * @param	array	$data
	 * @return	string
	 */
	static public function composeCsv(array $data);

	/**
	 * Removes only the values from the list and converts them into a string
	 * where each item is separated by $sep
	 * 
	 * @param	array	$data
	 * @param	string	$sep
	 * @return	string
	 */
	static public function composeList(array $data, $sep = ' ');
	
	/**
	 * @param	string	$str
	 * @return	string
	 */
	static public function composeString($str);

	/**
	 * @param	array	$list
	 * @param	string	$sep
	 * @return	string
	 */
	static public function composeArray(array $list, $sep = ' ');

	/**
	 * @return	TemplateCompositorInterface
	 */
	static public function getFileCompositor();

	/**
	 * @param	FileCompositorInterface $comp
	 * @return	null
	 */
	static public function setFileCompositor(FileCompositorInterface $comp);
		
	/**
	 * @return	FileCompositorInterface
	 */
	static public function loadFileCompositor();

	/**
	 * @return	bool
	 */
	static public function isFileCompositor();

	/**
	 * @return	null
	 */
	static public function createFileCompositor();
	
	/**
	 * @return	FileFinderInterface
	 */
	static public function getFileFinder();

	/**
	 * @param	FileFinderInterface $finder
	 * @return	null
	 */
	static public function setFileFinder(FileFinderInterface $finder);

	/**
	 * @return	bool
	 */
	static public function isFileFinder();

	/**
	 * @param	string	$path	
	 * @param	bool	$isBase
	 * @return	FileFinderInterface
	 */
	static public function createFileFinder($path = null, $isBase = true);

	/**
	 * @return	FileFinderInterface
	 */
	static public function loadFileFinder();
}
