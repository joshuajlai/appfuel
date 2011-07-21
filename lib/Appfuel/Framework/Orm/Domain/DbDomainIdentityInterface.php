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
namespace Appfuel\Framework\Orm\Domain;

/**
 * The domain identity is responsible for mapping datasource properity names
 * to the corresponding domain.
 */
interface DbDomainIdentityInterface
{
	public function getMap();
	public function setMap(array $map);
	public function getTable();
	public function setTable($name);
	public function getPrimaryKey();
	public function setPrimaryKey(array $key);
	public function getLabel();
	public function setLabel($label);
	public function getDependencies();
	public function setDependencies(array $list);
}
