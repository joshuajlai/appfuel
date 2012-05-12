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
	InvalidArgumentException;

/**
 */
interface PagePkgInterface extends PkgInterface
{
	/**
	 * @return	string
	 */
	public function getHtmlDocName();

	/**
	 * @return	string
	 */
	public function getMarkupFile();

	/**
	 * @return	string
	 */
	public function getJsInitFile();

	/**
	 * @return	bool
	 */
	public function isJsInitFile();

	/**
	 * @return	string
	 */
	public function getLayers();
}
