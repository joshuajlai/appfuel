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
namespace TestFuel\Provider;

use StdClass,
	SplFileInfo;

/**
 * Encapsulates on the general cases for getting sets string values to be
 * used with phpunits dataProvider
 */
class StringProvider implements StringProviderInterface
{
	/**
	 * @return	array
	 */
	public function provideAllStrings()
	{
		$empty   = $this->provideEmptyStrings();
		$regular = $this->provideNonEmptyStrings();
		$cast    = $this->provideCastableValues();
		return array_merge($empty, $regular, $cast);
	}

	/**
	 * @return	array
	 */
	public function provideCastableValues()
	{
		return array(
			array(12345),
			array(1.2345),
			array(true),
			array(false),
			array(null),
			array(new SplFileInfo('some/path'))
		);
	}

	/**
	 * @return	array
	 */
	public function provideNumericStrings()
	{
		return array(
			array('0'),
			array('1'),
			array('-1'),
			array('12345'),
			array('1.2345'),
		);
	}

	/**
	 * @return array
	 */
	public function provideEmptyNonEmptyAndToString()
	{
		$empty = $this->provideEmptyString();
		$nonEmpty = $this->provideNonEmptyString();
		$callable = array(array(new SplFileInfo('my/file')));
		return array_merge($empty, $nonEmpty, $callable);
	}

	/**
	 * @return	array
	 */
	public function provideEmptyStrings()
	{
		return array(
			array(''),
			array(' '),
			array('                                                      '),
			array("\t"),
			array("\n"),
			array("\r"),
			array("\0"),
			array("\x0B"),
			array("\x0b \t\n\r\0"),
		);
	}

	/**
	 * List of simple strings 
	 *
	 * @return	array
	 */
	public function provideNonEmptyStrings($isNumeric = true)
	{
		$result = array(
			array('true'),
			array('false'),
			array('key'),
			array('dashed-key'),
			array('string with spaces'),
			array('string:colons'),
			array('string,commas,'),
			array('string;semi;colon'),
			array('longerstringwithnospacesnotusedforkeysofanykindaswired'),
		);
	
		if (false === $isNumeric) {
			return $result;
		}	
		return array_merge($result, $this->provideNumericStrings());
	}

	/**
	 * @return	array
	 */
	public function provideStrictInvalidStrings($isNull = true)
	{
		$results = array(
            array(0),
            array(-1),
            array(1),
            array(12345),
            array(1.23454),
            array(true),
            array(false),
            array(new StdClass()),
            array(array()),
			array(array('string')),
            array(array(1)),
            array(array(1,2,3)),
        );

		if (true === $isNull) {
			$results[] = array(null);
		}

		return $results;
	}

	/**
	 * @return	array
	 */
	public function provideNoCastableStrings()
	{
		return array(
            array(array()),
			array(array('string')),
            array(array(1,2,3)),
            array(new StdClass()),
		);
	}
}	
