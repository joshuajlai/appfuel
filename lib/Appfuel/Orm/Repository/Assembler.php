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
	Appfuel\Framework\Orm\Repository\CriteriaInterface,
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

	/**
	 * @param	CriteriaInterface	$criteria
	 * @return	mixed	
	 */
	public function buildData(CriteriaInterface $criteria, array $data)
	{
		$err = 'buildData failed: ';
		$domainKey = $criteria->get('domain-key', false);
		$builder = $this->getDataBuilder();
		$custom	 = $criteria->get('custom-build', false);
		$params  = $criteria->get('custom-build-params', false);

		/*
		 * Allow for custom functions or closures to be used to build
		 * domain data. When closures are used they must follow the format
		 * closure($domain-key, array $data). custom callbacks can do as they
		 * please with the exception that data is alway prepended to the 
		 * front of the argument list
		 */
		if (is_callable($custom)) {
			if ($custom instanceof Closure) {
				return $custom($domainKey, $data);
			}

			if (! is_array($params)) {
				$params[] = $data;
			}
			else {
				unshift($params, $data);
				return call_user_func_array($custom, $params);
			}
		}

		/* for regular builds make sure the domain key exists */
		if (! $domainKey) {
			throw new Exception("$err domain key is missing");
		}

		/* 
		 * The criteria determine the actual method we look for in the
		 * data builder allowing custom functions to just be added onto 
		 * the data builder which uses the standard function signature.
		 * The default is of course 'buildDomainObject'
		 */
		$method = $criteria->get('build-method', false);
		if (! $method) {
			$method = 'buildDomainModel';
		}

		if (empty($method) || ! is_string($method)) {
			$err .= "DataBuilder method must be a non empty string";
			throw new Exception($err);
		}

		/*
		 * return the already pre mapped array of data
		 */
		if ('no-build' === $method) {
			return $data;
		}

		if (! method_exists($builder, $method)) {
			throw new Exception("$err DataBuilder method ($method) not found");
		}

		return $builder->$method($domainKey, $data);
	}
}
