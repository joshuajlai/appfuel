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
namespace TestFuel\Unit\View\Html\Tag;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\Tag\TagContent;

/**
 */
class TagContentTest extends BaseTestCase
{
    /**
     * System under test
     * @var TagContent
     */
    protected $content = null;

    /**
     * @return null
     */
    public function setUp()
    {   
        $this->content = new TagContent();
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
		$this->content = null;
    }

	/**
	 * @return	TagAttribute
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\TagContentInterface',
			$this->content
		);

		$this->assertEquals(' ', $this->content->getSeparator());
		$this->assertEquals(0, $this->content->count());
		$this->assertEquals(array(), $this->content->get());
	}

	/**
	 * @dataProvider  provideNonEmptyStrings
	 * @return	null
	 */
	public function testGetSetSeparator($char)
	{
		$this->assertSame($this->content, $this->content->setSeparator($char));
		$this->assertEquals((string)$char, $this->content->getSeparator());
	}

	/**
	 * We do not trim the separator
	 *
	 * @dataProvider  provideEmptyStrings
	 * @return	null
	 */
	public function testGetSetSeparatorEmptyString($char)
	{
		$this->assertSame($this->content, $this->content->setSeparator($char));
		$this->assertEquals($char, $this->content->getSeparator());
	}

	/**
	 * @return	array
	 */
	public function provideNonScalar()
	{
		return array(
			array(new StdClass()),
			array(array()),
			array(array(1,2,3))
		);
	}

	/**
	 * We do not trim the separator
	 *
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideNonScalar
	 * @return				null
	 */
	public function testGetSetSeparatorNoScalarString($char)
	{
		$this->content->setSeparator($char);
	}

	/**
	 * @dataProvider		provideNonEmptyStrings
	 * @return				null
	 */
	public function testAddWhenEmptyDefaultValidStrings($content)
	{
		$this->assertSame($this->content, $this->content->add($content));
		$this->assertEquals(1, $this->content->count());
		
		$expected = array(trim($content));
		$this->assertEquals($expected, $this->content->get());
		$this->assertEquals(trim($content), $this->content->get(0));
	}

	/**
	 * @return				null
	 */
	public function testAddObjectSupportsToString()
	{
		$content = 'i am content';
		$data = new SplFileInfo($content);
		$this->assertSame($this->content, $this->content->add($data));
		$this->assertEquals(1, $this->content->count());
		
		$expected = array(trim($content));
		$this->assertEquals($expected, $this->content->get());
		$this->assertEquals(trim($content), $this->content->get(0));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideNonScalar
	 * @return				null
	 */
	public function testAddInvalidData_Failure($data)
	{
		$this->content->add($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideEmptyStrings
	 * @return				null
	 */
	public function testAddInvalidActionEmpty_Failure($action)
	{
		$this->content->add('content', $action);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return				null
	 */
	public function testAddInvalidString_Failure($action)
	{
		$this->content->add('content', $action);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddInvalidAction_Failure()
	{
		$this->content->add('content', 'not-valid-action');
	}


	/**
	 * @return	null
	 */
	public function testAddAppend()
	{
		$block1 = 'block 1';
		$block2 = 'block 2';
		$block3 = 'block 3';
		
		$this->content->add($block1, 'append')
					  ->add($block2, 'append')
					  ->add($block3, 'append');

		$this->assertEquals(3, $this->content->count());
		
		$expected = array($block1, $block2, $block3);
		$this->assertEquals($expected, $this->content->get());

		$this->assertEquals($block1, $this->content->get(0));
		$this->assertEquals($block2, $this->content->get(1));
		$this->assertEquals($block3, $this->content->get(2));
	}

	/**
	 * @return	null
	 */
	public function testAddPrepend()
	{
		$block1 = 'block 1';
		$block2 = 'block 2';
		$block3 = 'block 3';
		
		$this->content->add($block1, 'prepend')
					  ->add($block2, 'prepend')
					  ->add($block3, 'prepend');

		$this->assertEquals(3, $this->content->count());
		
		$expected = array($block3, $block2, $block1);
		$this->assertEquals($expected, $this->content->get());

		$this->assertEquals($block1, $this->content->get(2));
		$this->assertEquals($block2, $this->content->get(1));
		$this->assertEquals($block3, $this->content->get(0));
	}

	/**
	 * @return	null
	 */
	public function testAddReplace()
	{
		$block1 = 'block 1';
		$block2 = 'block 2';
		$block3 = 'block 3';
		
		$this->content->add($block1, 'replace')
					  ->add($block2, 'Replace')
					  ->add($block3, 'REPLACE');

		$this->assertEquals(1, $this->content->count());
		
		$expected = array($block3);
		$this->assertEquals($expected, $this->content->get());

		$this->assertFalse($this->content->get(2));
		$this->assertFalse($this->content->get(1));
		$this->assertEquals($block3, $this->content->get(0));
	}

	/**
	 * @return	null
	 */
	public function testGetNotInteger()
	{
		$block1 = 'block 1';
		$block2 = 'block 2';
		$block3 = 'block 3';
		
		$this->content->add($block1)
					  ->add($block2)
					  ->add($block3);


		$this->assertFalse($this->content->get('this is a string'));
		$this->assertFalse($this->content->get(1.2345));
		$this->assertFalse($this->content->get(array(1,2,3)));
		$this->assertFalse($this->content->get(new StdClass()));
	}

	public function testClear()
	{
		$block1 = 'block 1';
		$block2 = 'block 2';
		$block3 = 'block 3';
		
		$this->content->add($block1)
					  ->add($block2)
					  ->add($block3);


		$this->assertEquals(3, $this->content->count());

		$this->assertTrue($this->content->clear());
		$this->assertEquals(0, $this->content->count());

		$this->content->add($block1)
					  ->add($block2)
					  ->add($block3);


		$this->assertEquals(3, $this->content->count());

		$this->assertTrue($this->content->clear(0));
		$this->assertEquals($block2, $this->content->get(0));

		$this->assertTrue($this->content->clear(1));
		$this->assertEquals($block2, $this->content->get(0));
		$this->assertFalse($this->content->get(1));

		$this->assertTrue($this->content->clear(0));
		$this->assertFalse($this->content->get(0));

		$this->content->add($block1)
					  ->add($block2)
					  ->add($block3);

		$this->assertFalse($this->content->clear(9));
		$this->assertFalse($this->content->clear('asdsad'));
		$this->assertFalse($this->content->clear(array(1,2,3)));
		$this->assertFalse($this->content->clear(new StdClass()));
		
	}

	/**
	 * @return	null
	 */
	public function testBuildDefaultSeparator()
	{
		$block1 = 'block 1';
		$block2 = 'block 2';
		$block3 = 'block 3';
		
		$this->content->add($block1)
					  ->add($block2)
					  ->add($block3);


		$expected = "$block1 $block2 $block3";
		$this->assertEquals($expected, $this->content->build());
	}

	/**
	 * @return	null
	 */
	public function testBuildSetSeparator()
	{
		$block1 = 'block 1';
		$block2 = 'block 2';
		$block3 = 'block 3';
		
		$this->content->add($block1)
					  ->add($block2)
					  ->add($block3);

		$this->content->setSeparator(':');
		$expected = "$block1:$block2:$block3";
		$this->assertEquals($expected, $this->content->build());

		$this->content->setSeparator('');
		$expected = "{$block1}{$block2}{$block3}";
		$this->assertEquals($expected, $this->content->build());
	}

	/**
	 * @return	null
	 */
	public function testBuildEmpty()
	{
		$this->assertEquals('', $this->content->build());
	}

	/**
	 * @return	null
	 */
	public function testToString()
	{
		$block1 = 'block 1';
		$block2 = 'block 2';
		$block3 = 'block 3';
		
		$this->content->add($block1)
					  ->add($block2)
					  ->add($block3);


		$expected = "$block1 $block2 $block3";
		$this->expectOutputString($expected);
		echo $this->content;
	}



}
