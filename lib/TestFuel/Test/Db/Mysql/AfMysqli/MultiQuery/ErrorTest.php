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
namespace TestFuel\Test\Db\Mysql\AfMysqli\MultiQuery;

use TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Mysql\AfMysqli\MultiQuery\Error;

/**
 * Test the adapters ability to wrap mysqli
 */
class ErrorTest extends BaseTestCase
{
	/**
	 * @return	null
	 */
	public function testIndex()
	{
		$index = 1;
		$code  = 9;
		$text  = 'this is an error';
		$sqlState = 'sqlstateCode';

		$error = new Error($index, $code, $text, $sqlState);

		$this->assertInstanceOf(
			'Appfuel\Framework\Db\DbErrorInterface',
			$error
		);
			
		$this->assertInstanceOf(
			'Appfuel\Db\DbError',
			$error
		);
			
		$this->assertEquals($index, $error->getIndex());
		$this->assertEquals($code, $error->getCode());
		$this->assertEquals($text, $error->getMessage());
		$this->assertEquals($sqlState, $error->getSqlState());	

	}

    /**
     * @return null
     */
    public function testToStringOutput()
    {
 		$index = 3;
		$code  = 9;
		$text  = 'this is an error';
		$sqlState = 'HY001';

		$error = new Error($index, $code, $text, $sqlState);

 
        $expected = 'ERROR 9 (HY001) on dataset (3): this is an error';
        $this->expectOutputString($expected);

        echo $error;
    }
	
}
