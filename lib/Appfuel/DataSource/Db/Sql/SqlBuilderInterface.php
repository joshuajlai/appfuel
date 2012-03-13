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
namespace Appfuel\DataSource\Db\Mysql\Sql\Dml\Select;

use Appfuel\DataStructure\DictionaryInterface;

/**
 */
interface SqlBuilderInterface
{
	/**
	 * @param	string	$type
	 * @param	DictionaryInterface		$data
	 * @return	string
	 */
	public function build($type, DictionaryInterface $data);

	/**
	 * @param	DictionaryInterface $data
	 * @return	string
	 */
	public function buildSelect(DictionaryInterface $data);

	/**
	 * @param	DictionaryInterface $data
	 * @return	string
	 */
	public function buildUpdate(DictionaryInterface $data);

	/**
	 * @param	DictionaryInterface $data
	 * @return	string
	 */
	public function buildInsert(DictionaryInterface $data);

	/**
	 * @param	DictionaryInterface $data
	 * @return	string
	 */
	public function buildDelete(DictionaryInterface $data);
}
