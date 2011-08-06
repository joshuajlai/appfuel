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
namespace Appfuel\Orm\Repository;

use BadMethodCallException,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Domain\DataBuilderInterface,
	Appfuel\Framework\Orm\Source\SourceHandlerInterface,
	Appfuel\Framework\Orm\Repository\AssemblerInterface;

/**
 * The assembler proxies data between two objects, the SourceHandler and
 * the DataBuilder. The source handler performs operations on the data source
 * and hands back a response. The data builder takes that response and shapes
 * the data into an appropriate format which could a domain object, array, 
 * string, etc...
 */
class Assembler implements AssemblerInterface
{
	/**
	 * Handles actual operations to and from the data source
	 * @var	DataSourceInterface
	 */
	protected $sourceHandler = null;

	/**
	 * Used to build data for different shapes like a domain object or 
	 * an array, or string with mapped data
	 * @var DomainBuilder
	 */
	protected $dataBuilder = null;

	/**
	 * @param	AssemblerInterface $asm
	 * @return	OrmRepository
	 */
	public function __construct(SourceHandlerInterface $sourceHandler,
								DataBuilderInterface   $dataBuilder)
	{
		$this->sourceHandler = $sourceHandler;
		$this->dataBuilder   = $dataBuilder;
	}

	/**
	 * @return	SourceHandlerInterface
	 */
	public function getSourceHandler()
	{
		return $this->sourceHandler;
	}

	/**
	 * @return	DataBuilderInterface
	 */
	public function getDataBuilder()
	{
		return $this->dataBuilder;
	}
}
