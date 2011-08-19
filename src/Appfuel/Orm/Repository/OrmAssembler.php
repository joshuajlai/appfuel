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
	Appfuel\Framework\AppfuelErrorInterface,
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
class OrmAssembler implements AssemblerInterface
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
	 * @param	CriteriaInterface $src	criteria for source handler
	 * @param	CriteriaInterface $bld	criteria for data builder
	 * @return	mixed
	 */
	public function process(CriteriaInterface $criteria)
	{
		$data = $this->executeSource($criteria);
		if ($data instanceof AppfuelErrorInterface) {
			return $data;
		}
	
		/* 
		 * some data does not require a build process and results can be
		 * returned as is
		 */	
		if (true === $criteria->get('ignore-build', false)) {
			return $data;
		}
	
		$ignoreType = $criteria->get('ignore-return-type', false);

		/*
		 * you would ignore the array type if you have a custom build
		 * that expects something other than an array
		 */ 
		if (false === $ignoreType && ! is_array($data)) {
			$err  = "processData failed: data returned from the source handler";
			$err .= " must be an array";
			throw new Exception($err);
		}

		return $this->buildData($criteria, $data);
	}

	/**
	 * @param	CriteriaInterface $criteria describes what to to be 
	 * @return	OrmResponseInterface
	 */
	public function executeSource(CriteriaInterface $criteria)
	{
		$err	 = 'executeDataSource failed: ';
		$source  = $this->getSourceHandler();
		
		/* 
		 * Find the correct method in the source handle to execute
		 */
		$method = $criteria->get('source-method', false);
		if (empty($method) || ! is_string($method)) {
			$err .= "SourceHandler method must be a non empty string";
			throw new Exception($err);
		}

		if (! method_exists($source, $method)) {
			$err .= "SourceHandler method ($method) not found";
			throw new Exception($err);
		}

		return $source->$method($criteria);
	}

	/**
	 * This can create a brand new domain object to be inserted into the
	 * the datasource. It can populate data into the domain or just create
	 * an empty domain, either way the domain is marked new.
	 *
	 * @param	array	$data	
	 * @return	mixed
	 */
	public function createNewDomainObject($key, array $data = null)
	{
		$builder = $this->getDataBuilder();
		return $builder->buildDomainModel($key, $data, true);
	}

	/**
	 * Build domain models or objects. Any domain model will be marked as 
	 * marshalled.
	 *
	 * @param	CriteriaInterface	$criteria
	 * @return	mixed	
	 */
	public function buildData(CriteriaInterface $criteria, $data)
	{
		$err	 = 'buildData failed: ';
		$builder = $this->getDataBuilder();
		$domainKey = $criteria->get('domain-key', false);
		$custom	 = $criteria->get('custom-build', false);
		$params  = $criteria->get('custom-build-params', false);

		/*
		 * Allow for custom functions or closures to be used to build
		 * domain data. When closures are used they must follow the format
		 * front of the argument list
		 */
		if (is_callable($custom)) {
			/* 
			 * no params supplied so add data to the param list otherwise
			 * prepend data to be the first param in the function signature
			 */
			if (! is_array($params)) {
				$params[] = $domainKey;
				$params[] = $data;
			}
			else {
				array_unshift($params, $data);
				array_unshift($params, $domainKey);
			}
				
			return call_user_func_array($custom, $params);
		}
		else if (! empty($custom) && ! is_callable($custom)) {
			throw new Exception("$err custom build declared but not callable");
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

		if (! method_exists($builder, $method)) {
			throw new Exception("$err DataBuilder method ($method) not found");
		}

		return $builder->$method($domainKey, $data);

	}
}
