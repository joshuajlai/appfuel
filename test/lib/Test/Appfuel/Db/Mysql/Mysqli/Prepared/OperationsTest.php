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
namespace Test\Appfuel\Db\Mysql\Mysqli\Prepared;

use Appfuel\Db\Mysql\Mysqli\Prepared\Stmt,
	StdClass,
	mysqli_result;

/**
 * Test actual sql operations for prepared statements. Tests select,insert,
 * update and delete
 */
class OperationsTest extends StmtTestCase
{

	/**
	 * @return array
	 */
	public function provideSqlQueryId_1()
	{
		$sql = 'SELECT query_id, param_1, param_2, param_3, result ' .
			   'FROM   test_queries ' .
			   'WHERE  query_id = ?';

		return array(
			array($sql, array(1))
		);
	}

	/**
	 * @dataProvider	provideSqlQueryId_1
	 * @return	null
	 */
	public function testSimpleSelectOneRow($sql, $values)
	{
		$this->assertTrue($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->organizeParams($values));
		$this->assertTrue($this->stmt->execute());
		$this->assertTrue($this->stmt->organizeResults());
		
		$result = $this->stmt->fetch();
		$expected = array(
			array(
				'query_id' => 1,
				'param_1'  => 1,
				'param_2'  => 'code_a',
				'param_3'  => 0,
				'result'   => 'query issued'
			),
		);
		$this->assertEquals($expected, $result);
		$this->stmt->close();
	}

	/**
	 * We are going to run the simple query than reset the stmt and run it
	 * with new parameters
	 *
	 * @dataProvider	provideSqlQueryId_1
	 * @return	null
	 */
	public function testSimpleSelectOneRowReset($sql, $values)
	{
		$this->stmt->prepare($sql);
		$this->assertTrue($this->stmt->isPrepared());

		$this->stmt->organizeParams($values);
		$this->stmt->execute();
		$this->assertTrue($this->stmt->isExecuted());
		
		$this->stmt->organizeResults();
		$this->assertTrue($this->stmt->isBoundResultset());
		$this->assertTrue($this->stmt->isResultset());

		$this->stmt->storeResults();
		$this->assertTrue($this->stmt->isBufferedResultset());

		
		$result   = $this->stmt->fetch();
		$expected = array(
			array(
				'query_id' => 1,
				'param_1'  => 1,
				'param_2'  => 'code_a',
				'param_3'  => 0,
				'result'   => 'query issued'
			),
		);

		$this->assertEquals($expected, $result);

		$this->assertTrue($this->stmt->reset());
		$this->assertFalse($this->stmt->isPrepared());
		$this->assertFalse($this->stmt->isBufferedResultset());
		$this->assertFalse($this->stmt->isError());

		$this->assertTrue($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->organizeParams(array(2)));
		$this->assertTrue($this->stmt->execute());
		$this->assertTrue($this->stmt->organizeResults());
	
		$result   = $this->stmt->fetch();
		$expected = array(
			array(
				'query_id' => 2,
				'param_1'  => 0,
				'param_2'  => 'code_b',
				'param_3'  => 1,
				'result'   => 'query 2 issued'
			),
		);
		$this->assertEquals($expected, $result);
		$this->stmt->close();
	}

	/**
	 * @dataProvider	provideSqlQueryId_1
	 * @return	null
	 */
	public function testBindMoreThanOneQuery($sql, $values)
	{
		$this->stmt->prepare($sql);
		$this->stmt->organizeParams($values);
		$this->stmt->execute();
		$this->stmt->organizeResults();
		$this->stmt->storeResults();
		
		$result   = $this->stmt->fetch();
		$expected = array(
			array(
				'query_id' => 1,
				'param_1'  => 1,
				'param_2'  => 'code_a',
				'param_3'  => 0,
				'result'   => 'query issued'
			),
		);
		$this->assertEquals($expected, $result);

		
		$this->assertTrue($this->stmt->organizeParams(array(2)));
		$this->stmt->execute();
		$result   = $this->stmt->fetch();
		$expected = array(
			array(
				'query_id' => 2,
				'param_1'  => 0,
				'param_2'  => 'code_b',
				'param_3'  => 1,
				'result'   => 'query 2 issued'
			),
		);
		$this->assertEquals($expected, $result);

		$this->assertTrue($this->stmt->organizeParams(array(3)));
		$this->stmt->execute();
		$result   = $this->stmt->fetch();
		$expected = array(
			array(
				'query_id' => 3,
				'param_1'  => 1,
				'param_2'  => 'code_c',
				'param_3'  => 2,
				'result'   => 'query 3 issued'
			),
		);
		$this->assertEquals($expected, $result);
		$this->stmt->close();
	}

	/**
	 * We will insert 3 rows check that they exist then delete them
	 * 
	 * @return null
	 */
	public function testInsertDeleteUpdate()
	{
		$select = 'SELECT query_id, param_1, param_2, param_3, result ' .
				  'FROM test_queries WHERE query_id IN (?,?,?)';
	
		$this->assertTrue($this->stmt->prepare($select));
		$this->stmt->organizeParams(array(99,100,101));
		$this->stmt->execute();
		$this->stmt->organizeResults();

		$results = $this->stmt->fetch();
		$expected = array();
		$this->assertEquals($expected, $results);

		$insert = 'INSERT INTO test_queries ' .
				'(query_id, param_1, param_2, param_3, result) ' .
				'VALUES (?, ?, ?, ?, ?) ';

		$values = array(99, 88, 'code_z', 0, 'query 99 issued');
		
		$this->stmt->reset();
		$this->stmt->prepare($insert);
		$this->stmt->organizeParams($values);
		$this->assertTrue($this->stmt->execute());

		$values = array(100, 87, 'code_zz', 1, 'query 100 issued');
		$this->stmt->organizeParams($values);
		$this->assertTrue($this->stmt->execute());

		$values = array(101, 86, 'code_zzz', 0, 'query 101 issued');
		$this->stmt->organizeParams($values);
		$this->assertTrue($this->stmt->execute());

	
		$this->stmt->reset();
		$this->assertTrue($this->stmt->prepare($select));
		$this->stmt->organizeParams(array(99,100,101));
		$this->stmt->execute();
		$this->stmt->organizeResults();

		$results  = $this->stmt->fetch();
		$expected = array(
			array(
				'query_id' => 99,
				'param_1'  => 88,
				'param_2'  => 'code_z',
				'param_3'  => 0,
				'result'   => 'query 99 issued'
			),
			array(
				'query_id' => 100,
				'param_1'  => 87,
				'param_2'  => 'code_zz',
				'param_3'  => 1,
				'result'   => 'query 100 issued'
			),
			array(
				'query_id' => 101,
				'param_1'  => 86,
				'param_2'  => 'code_zzz',
				'param_3'  => 0,
				'result'   => 'query 101 issued'
			),
		);
		$this->assertEquals($expected, $results);

		$delete = 'DELETE FROM test_queries WHERE query_id IN (?,?,?)';
		$this->stmt->reset();
		$this->assertTrue($this->stmt->prepare($delete));
		$this->stmt->organizeParams(array(99,100,101));
		$this->stmt->execute();
		$this->stmt->organizeResults();

		$this->stmt->reset();
		$this->assertTrue($this->stmt->prepare($select));
		$this->stmt->organizeParams(array(99,100,101));
		$this->stmt->execute();
		$this->stmt->organizeResults();

		$results = $this->stmt->fetch();
		$expected = array();
		$this->assertEquals($expected, $results);


		$this->stmt->close();
	}

	public function testInsertUpdateDelete()
	{
		$select = 'SELECT query_id, param_1, param_2, param_3, result ' .
				  'FROM test_queries WHERE query_id = ?';
	
		$this->assertTrue($this->stmt->prepare($select));
		$this->stmt->organizeParams(array(99));
		$this->assertTrue($this->stmt->execute());
		$this->stmt->organizeResults();
		$expected = array();
		$this->assertEquals($expected, $this->stmt->fetch());

		$insert = 'INSERT INTO test_queries ' .
				'(query_id, param_1, param_2, param_3, result) ' .
				'VALUES (?, ?, ?, ?, ?) ';

		$values = array(99, 88, 'code_z', 0, 'query 99 issued');
		
		$this->stmt->reset();
		$this->stmt->prepare($insert);
		$this->stmt->organizeParams($values);
		$this->assertTrue($this->stmt->execute());

		$this->stmt->reset();
		$this->assertTrue($this->stmt->prepare($select));
		$this->stmt->organizeParams(array(99));
		$this->assertTrue($this->stmt->execute());
		$this->stmt->organizeResults();
		$result = $this->stmt->fetch();
		$expected = array(
			array(
				'query_id' => 99,
				'param_1'  => 88,
				'param_2'  => 'code_z',
				'param_3'  => 0,
				'result'   => 'query 99 issued'
			),
		);
		$this->assertEquals($expected, $result);

		$update = 'Update test_queries ' .
				  'SET    param_1=?, param_2=?, param_3=?, result=? ' .
				  'WHERE  query_id = ?';

		$values = array(22, 'code_bbc', 1, 'update query passed', 99);	
		$this->stmt->reset();
		$this->assertTrue($this->stmt->prepare($update));
		$this->stmt->organizeParams($values);
		$this->assertTrue($this->stmt->execute());

		$this->stmt->reset();
		$this->assertTrue($this->stmt->prepare($select));
		$this->stmt->organizeParams(array(99));
		$this->assertTrue($this->stmt->execute());
		$this->stmt->organizeResults();
		$result = $this->stmt->fetch();
		$expected = array(
			array(
				'query_id' => 99,
				'param_1'  => 22,
				'param_2'  => 'code_bbc',
				'param_3'  => 1,
				'result'   => 'update query passed'
			),
		);
		$this->assertEquals($expected, $result);

		$delete = 'DELETE FROM test_queries WHERE query_id = ?';
		$this->stmt->reset();
		$this->assertTrue($this->stmt->prepare($delete));
		$this->stmt->organizeParams(array(99));
		$this->assertTrue($this->stmt->execute());

		$this->assertTrue($this->stmt->prepare($select));
		$this->stmt->organizeParams(array(99));
		$this->assertTrue($this->stmt->execute());
		$this->stmt->organizeResults();

		$expected = array();			
		$this->assertEquals($expected, $this->stmt->fetch());
		$this->stmt->close();
	}	
}
