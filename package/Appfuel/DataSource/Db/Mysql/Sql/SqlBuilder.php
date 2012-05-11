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

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\DataSource\Db\Sql\SqlBuilderInterface

/**
 */
class SqlBuilder implements SqlBuilderInterface
{
	public function buildSelect(DictionaryInterface $data)
	{
		$selectBuilder = new SelectBuilder();
		return $selectBuilder->build($data);
	}

	public function buildUpdate(DictionaryInterface $data)
	{
		$builder = new UpdateBuilder();
		return $builder->build($data);
	}

	public function buildInsert(DictionaryInterface $data)
	{
		$builder = new InsertBuilder();
		return $builder->build($data);
	}

	public function buildDelete(DictionaryInterface $data)
	{
		$builder = new DeleteBuilder();
		return $builder->build($data);
	}
}
