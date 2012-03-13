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
namespace Appfuel\DataSource\Db\Mysql\Sql\Select;

/**
 */
interface MysqlSelectBuilderInterface
{
	
	public function build(array $data);

	public function buildKeywords(array $data);

	/**
	 * @return	SelectKeywordsInterface
	 */
	public function getSelectKeywords();

	/**
	 * @param	SelectKeywordsInterface $keywords
	 * @return	null
	 */
	public function setSelectKeywords(SelectKeywordsInterface $keywords);

	/**
	 * @return	SelectKeywordInterface
	 */	
	public function createSelectKeywords();

}
