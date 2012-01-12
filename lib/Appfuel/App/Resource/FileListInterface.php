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

use Countable,
	Iterator;

/**
 */
interface FileListInterface extends Countable, Iterator
{
	/**
	 * @return	string
	 */
	public function getType();

    /**
     * @param   array   $list
     * @return  FileList
     */
    public function loadFiles(array $list);

    /**
     * @return  array
     */
    public function getFiles();
}
