<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Testfuel\Unit\Console;

use StdClass,
	Appfuel\Validate\FieldSpec,
	Testfuel\TestCase\BaseTestCase;

class FieldSpecTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideInvalidStringsWithEmpty()
	{
		$result = $this->provideInvalidStrings();
		$result[] = array('');
		return $result;
	}

	/**
	 * @return	array	
	 */
	public function provideInvalidStrings()
	{
		return array(
			array(12345),
			array(1.234),
			array(0),
			array(true),
			array(false),
			array(array(1,2,3)),
			array(new StdClass()),
		);
	}

	/**
	 * @param	array	$data
	 * @return	FieldSpec
	 */
	public function createFieldSpec(array $data)
	{
		return new FieldSpec($data);
	}

	/**
	 * @test
	 * @return	FieldSpec
	 */
	public function minimalFieldSpec()
	{
		$data = array(
			'field'  => 'id',
			'filter' => 'int',
			
		);
		$spec = $this->createFieldSpec($data);
		$this->assertInstanceOf('Appfuel\Validate\FieldSpecInterface', $spec);
		$this->assertEquals($data['field'], $spec->getFieldName());
	}
}
