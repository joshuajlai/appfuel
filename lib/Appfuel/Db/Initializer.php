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
namespace Appfuel\Db\Mysql\Adapter;

use mysqli,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Connection\DetailFactoryInterface,
	Appfuel\Framework\Db\InitializerInterface;

/**
 * The primary responsibility of the intitailzer is to initialize a list
 * of database strings into database connection objects and put them into the
 * the db resource pool.
 */
class Initializer extends InitializerInterface
{
	/**
	 * Used to build connectionDetail objects from connection strings
	 * @var DetailFactoryInterface
	 */
	protected $connDetailFactory = null;

	public function __construct(DetailFactoryInterface $factory)
	{
		
	}

	/**
	 * @return	DetailFactoryInterface
	 */
	public function getConnDetailFactory()
	{
		return $this->connDetail;
	}

	/**
	 * @param	DetailFactoryInterface
	 * @return	Initializer
	 */
	public function setConnDetailFactory(DetailFactoryInterface $factory)
	{
		$this->connDetailFactory = $factory;
		return $this;
	}

	public function initialize(array $connStrings)
	{

	}
}
