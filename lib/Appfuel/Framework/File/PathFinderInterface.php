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
namespace Appfuel\Framework\File;


/**
 * The file path finder encapsulates the knowledge of the base path and allows
 * modules like View to have an interface for resolving relative paths. For 
 * example a view template could swap out a path finder allowing templates to
 * live in completely different directories without the template class caring.
 */
interface PathFinderInterface
{
	public function getBasePath();
	public function disableBasePath();
	public function enableBasePath();
	public function isBasePathEnabled();
	public function getRelativeRootPath();
	public function setRelativeRootPath($path);
	public function resolveRootPath();
	public function getPath($path = null);
}
