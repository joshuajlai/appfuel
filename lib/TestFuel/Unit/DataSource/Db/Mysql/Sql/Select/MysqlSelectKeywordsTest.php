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
namespace TestFuel\Unit\DataSource\Db\Mysql\Sql\Select;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataSource\Db\Mysql\Sql\Select\MysqlSelectKeywords;

class SelectKeywordsTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var MysqlSelectKeywords
	 */
	protected $keywords = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->keywords	= new MysqlSelectKeywords();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->keywords = null;
	}

	/**
	 * @return MysqlSelectKeywords
	 */
	public function getSelectKeywords()
	{
		return $this->keywords;
	}

	/**
	 * @return	array
	 */
	public function provideInvalidSeparators()
	{
		return	array(
			array(null),
			array(''),
			array(12345),
			array(true),
			array(false),
			array(array(1,2,3)),
			array(new StdClass()),
		);
	}

	public function provideDistinctRowAll()
	{
		return array(
			array('ALL',		 array('ALL')),
			array('DISTINCT',	 array('DISTINCT')),
			array('DISTINCTROW', array('DISTINCTROW'))
		);
	}

	public function provideDistinctRowAllMultiple()
	{
		return array(
			array(array('all','distinct','distinctrow'), array('DISTINCTROW')),
			array(array('distinct','distinctrow','all'), array('ALL')),
			array(array('all','distinctrow','distinct'), array('DISTINCT')),
		);
	}

	public function provideResult()
	{
		return array(
			array('sql_big_result',		 array('SQL_BIG_RESULT')),
			array('sql_small_result',	 array('SQL_SMALL_RESULT')),
		);
	}

	public function provideResultMultiple()
	{
		$big = 'SQL_BIG_RESULT';
		$small = 'SQL_SMALL_RESULT';
		return array(
			array(array($big, $small), array($small)),
			array(array($small, $big), array($big)),
		);
	}

	public function provideCache()
	{
		return array(
			array('sql_cache',		 array('SQL_CACHE')),
			array('sql_no_cache',	 array('SQL_NO_CACHE')),
		);
	}

	public function provideCacheMultiple()
	{
		$cache   = 'SQL_CACHE';
		$nocache = 'SQL_NO_CACHE';
		return array(
			array(array($cache, $nocache), array($nocache)),
			array(array($nocache, $cache), array($cache)),
		);
	}
	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$keywords  = $this->getSelectKeywords();
		$interface = 'Appfuel\DataSource\Db\Mysql\Sql\Select' .
					 '\MysqlSelectKeywordsInterface';

		$this->assertInstanceof($interface, $keywords);
		$this->assertEquals(' ', $keywords->getSeparator());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetSetSeparator()
	{
		$sep = PHP_EOL;
		$keywords = $this->getSelectKeywords();
		$this->assertNull($keywords->setSeparator($sep));
		$this->assertEquals($sep, $keywords->getSeparator());

		$sep = PHP_EOL . "\t\t";
		$this->assertNull($keywords->setSeparator($sep));
		$this->assertEquals($sep, $keywords->getSeparator());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidSeparators	
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetSeparator_Failures($sep)
	{
		$keywords = $this->getSelectKeywords();
		$keywords->setSeparator($sep);
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testGetAllKeywords()
	{
		$expected = array(
			'ALL',
			'DISTINCT',
			'DISTINCTROW',
			'HIGH_PRIORITY',
			'STRAIGHT_JOIN',
			'SQL_SMALL_RESULT',
			'SQL_BIG_RESULT',
			'SQL_BUFFER_RESULT',
			'SQL_CACHE',
			'SQL_NO_CACHE',
			'SQL_CALC_FOUND_ROWS'
		);
		
		$keywords = $this->getSelectKeywords();
		$this->assertEquals($expected, $keywords->getAllKeywords());
	}

	/**
	 * @dataProvider	provideDistinctRowAll
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testEnableAllDistinctDistinctRow($in, $out)
	{
		$keywords = $this->getSelectKeywords();
		$this->assertNull($keywords->enableKeyword($in));

		$this->assertEquals($out, $keywords->getEnabledkeywords());
	}

	/**
	 * @dataProvider	provideDistinctRowAllMultiple
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testEnableKeywordsAllDistinctDistinctRow($in, $out)
	{
		$keywords = $this->getSelectKeywords();
		$this->assertNull($keywords->enableKeywords($in));

		$this->assertEquals($out, $keywords->getEnabledkeywords());
	}

	/**
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testStraightJoin()
	{
		$word = 'STRAIGHT_JOIN';
		$keywords = $this->getSelectKeywords();
		$this->assertNull($keywords->enableKeyword($word));
		
		$expected = array($word);
		$this->assertEquals($expected, $keywords->getEnabledkeywords());
	}

	/**
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testHighPriority()
	{
		$word = 'HIGH_PRIORITY';
		$keywords = $this->getSelectKeywords();
		$this->assertNull($keywords->enableKeyword($word));
		
		$expected = array($word);
		$this->assertEquals($expected, $keywords->getEnabledkeywords());
	}

	/**
	 * @dataProvider	provideResult
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testBigSmallResult($in, $out)
	{
		$keywords = $this->getSelectKeywords();
		$this->assertNull($keywords->enableKeyword($in));

		$this->assertEquals($out, $keywords->getEnabledkeywords());
	}

	/**
	 * @dataProvider	provideResultMultiple
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testBigSmallResultMultiple($in, $out)
	{
		$keywords = $this->getSelectKeywords();
		$this->assertNull($keywords->enableKeywords($in));

		$this->assertEquals($out, $keywords->getEnabledkeywords());
	}

	/**
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testBufferResult()
	{
		$word = 'sql_buffer_result';
		$keywords = $this->getSelectKeywords();
		$this->assertNull($keywords->enableKeyword($word));
		
		$expected = array(strtoupper($word));
		$this->assertEquals($expected, $keywords->getEnabledkeywords());
	}

	/**
	 * @dataProvider	provideCache
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testSqlCache($in, $out)
	{
		$keywords = $this->getSelectKeywords();
		$this->assertNull($keywords->enableKeyword($in));

		$this->assertEquals($out, $keywords->getEnabledkeywords());
	}

	/**
	 * @dataProvider	provideCacheMultiple
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testSqlCacheMultiple($in, $out)
	{
		$keywords = $this->getSelectKeywords();
		$this->assertNull($keywords->enableKeywords($in));

		$this->assertEquals($out, $keywords->getEnabledkeywords());
	}

	/**
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testFounRows()
	{
		$word = 'SQL_CALC_FOUND_ROWS';
		$keywords = $this->getSelectKeywords();
		$this->assertNull($keywords->enableKeyword($word));
		
		$expected = array(strtoupper($word));
		$this->assertEquals($expected, $keywords->getEnabledkeywords());
	}

	/**
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testAllKeywords()
	{
		$keywords = $this->getSelectKeywords();
        $words = array(
            'DISTINCT',
            'HIGH_PRIORITY',
            'STRAIGHT_JOIN',
            'SQL_BIG_RESULT',
            'SQL_BUFFER_RESULT',
            'SQL_CACHE',
            'SQL_CALC_FOUND_ROWS'
        );
		$this->assertNull($keywords->enableKeywords($words));

		$this->assertEquals($words, $keywords->getEnabledkeywords());
	}

}
