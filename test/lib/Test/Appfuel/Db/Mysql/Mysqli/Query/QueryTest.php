<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test\Appfuel\Db\Mysql\Mysqli\Query;

use Appfuel\Db\Mysql\Mysqli\Query;

/**
 * This class holds only the functionality to perform and debug queries.
 * The QueryTestCase holds all setUp and tearDown code aswell as 
 * data providers
 */
class QueryTest extends QueryTestCase
{

	/**
	 * The handle is made immutable by passing it through the constructor
	 * and having no public setter.
	 *
	 * @return	null
	 */
	public function testGetHandle()
	{
		$this->assertInstanceOf('mysqli', $this->query->getHandle());
	}

	/**
	 * @return	null
	 */
	public function testBufferedSendQuery()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		$result = $this->query->execute($sql);
		$this->assertInstanceOf(
			'Appfuel\Db\DbResponse',
			$result
		);
		$this->assertEquals(1, $result->getRowCount());

		$expected = array(
			'query_id' => 1,
			'result'   => 'query issued'
		);
		$this->assertEquals($expected, $result->getCurrentResult());
        
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		
		/* this will not fail even though we did not free the result */
		$result = $this->query->execute($sql);
		$this->assertInstanceOf(
			'Appfuel\Db\DbResponse',
			$result
		);
		$this->assertEquals(1, $result->getRowCount());


		$expected = array(
			'query_id' => 2,
			'result'   => 'query 2 issued'
		);
		$this->assertEquals($expected, $result->getCurrentResult());
        
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=3';
		$result = $this->query->execute($sql);
		$this->assertInstanceOf(
			'Appfuel\Db\DbResponse',
			$result
		);
		$this->assertEquals(1, $result->getRowCount());
		
		$expected = array(
			'query_id' => 3,
			'result'   => 'query 3 issued'
		);
		$this->assertEquals($expected, $result->getCurrentResult());
	}

	/**
	 * The query class always frees the result so you don't have to worry
	 * about use and store result flags from the prespective of having to
	 * free results
	 *
	 * @return	null
	 */
	public function xtestUnBufferedSendQuery()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		$result = $this->query->execute($sql, MYSQLI_USE_RESULT);
		
		$expected = array(
			'row-count' => 1,
			'resultset' => array(
				array(
					'query_id' => 1,
					'result'   => 'query issued'
				)
			)
		);
		$this->assertEquals($expected, $result);
		
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		$result = $this->query->execute($sql);
		$expected = array(
			'row-count' => 1,
			'resultset' => array(
				array(
					'query_id' => 2,
					'result'   => 'query 2 issued'
				)
			)
		);
		$this->assertEquals($expected, $result);
	}
}
