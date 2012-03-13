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

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\DataStructure\DictionaryInterface;

/**
 */
class SelectBuilder implements MysqlSelectBuilderInterface
{
	/**
	 * @var	SelectKeywords
	 */
	protected $keywords = null;

	/**
	 * @return	SelectBuilder
	 */
	public function __construct()
	{
		$this->keywords = $this->createSelectKeywords();
	}
	
	public function build(array $data)
	{
		$sql = 'SELECT ';
		if (isset($data['keywords']) && is_array($data['keywords'])) {
			$keySep = null;
			if (isset($data['keyword-sep']) && 
				is_string($data['keyword-sep'])) {
					$keySep = $data['keyword-sep'];
			}
			$sql .= $this->buildKeywords($data['keywords'], $keySep);
		}
	}

	/**
	 * @param	array	$data
	 * @param	string	$separator
	 * @return	string
	 */
	public function buildKeywords(array $data, $separator = null)
	{
		$keywords = $this->getKeywords();
		$keywords->enableKewords($data);

		if (null !== $separator) {
			$keywords->setSeparator($seperator);
		}
		$result = $keywords->build();
		$keywords->clear();

		return $result;
	}

	/**
	 * @return	SelectKeywordsInterface
	 */
	public function getSelectKeywords()
	{
		return $this->keywords;
	}

	/**
	 * @param	SelectKeywordsInterface $keywords
	 * @return	null
	 */
	public function setSelectKeywords(SelectKeywordsInterface $keywords)
	{
		$this->keywords = $keywords;
	}

	/**
	 * @return	SelectKeywords
	 */
	public function createSelectKeywords()
	{
		return new SelectKeywords();
	}
}
