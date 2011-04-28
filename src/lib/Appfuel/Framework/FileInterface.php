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
namespace Appfuel\Framework;


/**
 */
interface FileInterface
{
	public function getFullPath();
	public function isFile();
	public function isDir();
	public function isLink();
	public function isExecutable();
	public function isReadable();
	public function isWritable();
}
