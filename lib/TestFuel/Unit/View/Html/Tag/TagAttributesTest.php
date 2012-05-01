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
	Appfuel\View\Html\Tag\TagAttributes;

/**
 */
class TagAttributesTest extends BaseTestCase
{
    /**
     * System under test
     * @var TagAttributes
     */
    protected $attrs = null;

	/**
	 * List of global attributes
	 * @var array
	 */
	protected $globalAttrs = array(
        'accessKey',
        'class',
        'contextmenu',
        'dir',
        'draggable',
        'dropzone',
        'hidden',
        'id',
        'lang',
        'spellcheck',
        'style',
        'tabindex',
        'title'
	);

    /**
     * @return null
     */
    public function setUp()
    {   
        $this->attrs = new TagAttributes();
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
		$this->attrs = null;
    }

	/**
	 * @return	array
	 */
	public function getHtml5GlobalAttributes()
	{
		return $this->globalAttrs;
	}

	/**
	 * @return	TagAttribute
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\TagAttributesInterface',
			$this->attrs
		);

		$this->assertTrue($this->attrs->isValidation());
		$this->assertEquals(0, $this->attrs->count());

		return $this->attrs;
	}

	/**
	 * @depends	testInitialState
	 * @param	TagsAttributes $attrs
	 * @return	TagsAttributes
	 */
	public function testEnableDisableIsValidation(TagAttributes $attrs)
	{
		$this->assertSame($attrs, $attrs->disableValidation());
		$this->assertFalse($attrs->isValidation());
		
		$this->assertSame($attrs, $attrs->enableValidation());
		$this->assertTrue($attrs->isValidation());

		return $attrs;
	}

	/**
	 * @depends	testInitialState
	 * @param	TagsAttributes $attrs
	 * @return	TagsAttributes
	 */
	public function testGetGlobalAttributes(TagAttributes $attrs)
	{
		$globals = $attrs->getGlobalAttributes();
		$this->assertEquals(
			$this->getHtml5GlobalAttributes(),
			$globals
		);

		return array($attrs, $globals);
	}

	/**
	 * @depends	testGetGlobalAttributes
	 * @param	TagsAttributes $attrs
	 * @return	TagsAttributes
	 */
	public function testGetWhiteListEmpty(array $data)
	{
		$attrs   = $data[0];
		$globals = $data[1];

		$this->assertEquals($globals, $attrs->getWhiteList());
		$this->assertEquals($globals, $attrs->getWhiteList(true));
		$this->assertEquals(array(), $attrs->getWhiteList(false));
	
		return $attrs;
	}

	/**
	 * @dataProvider	provideNonEmptyStringsNoNumbers	
	 * @param			string	$attr
	 * @return			null
	 */
	public function testAddToWhiteListValidString($attr)
	{
		$this->assertSame($this->attrs, $this->attrs->addToWhiteList($attr));
		$this->assertEquals(array($attr), $this->attrs->getWhiteList(false));
	}

	/**
	 * @depends	testAddToWhiteListValidString
	 * @return	null
	 */
	public function testAddGlobalAttributesToWhiteList()
	{
		$this->assertEquals(array(), $this->attrs->getWhiteList(false));
		$attrs = $this->attrs->getGlobalAttributes();
		foreach ($attrs as $attr) {
			$this->assertSame(
				$this->attrs, 
				$this->attrs->addToWhiteList($attr)
			);
		}

		/* no attributes should be added because they all exist as global
		 */
		$this->assertEquals(array(), $this->attrs->getWhiteList(false));
	}

	/**
     * @depends testAddToWhiteListValidString
     * @return  null
	 */
	public function testAddToWhiteListDuplicates()
	{
		$attr1 = 'my-attr';
		$attr2 = 'my-other-attr';
		$global = $this->attrs->getGlobalAttributes();
		$this->assertNotContains($attr1, $global);
		$this->assertNotContains($attr2, $global);

		$this->attrs->addToWhiteList($attr1)
					->addToWhiteList($attr2)
					->addToWhiteList($attr1)
					->addToWhiteList($attr2)
					->addToWhiteList($attr1);

		$expected = array($attr1, $attr2);
		$this->assertEquals($expected, $this->attrs->getWhiteList(false));
	}

	/**
     * @depends testAddToWhiteListValidString
     * @return  null
	 */
	public function testClearWhiteList()
	{
		$attr1 = 'my-attr';
		$attr2 = 'my-other-attr';
		$this->attrs->addToWhiteList($attr1)
					->addToWhiteList($attr2);


		$expected = array($attr1, $attr2);
		$this->assertEquals($expected, $this->attrs->getWhiteList(false));
		
		$this->assertSame($this->attrs, $this->attrs->clearWhiteList());
		$this->assertEquals(array(), $this->attrs->getWhiteList(false));

		/* does not effect global attributes */	
		$global = $this->getHtml5GlobalAttributes();
		$this->assertEquals($global, $this->attrs->getGlobalAttributes());

		/* clear when already empty */
		$this->assertSame($this->attrs, $this->attrs->clearWhiteList());
		$this->assertEquals(array(), $this->attrs->getWhiteList(false));
		$this->assertEquals($global, $this->attrs->getGlobalAttributes());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideEmptyStrings
	 * @param				string	$attr
	 * @return				null
	 */
	public function testAddToWhiteListEmpyString_Failure($attr)
	{
		$this->assertSame($this->attrs, $this->attrs->addToWhiteList($attr));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @param				string	$attr
	 * @return				null
	 */
	public function testAddToWhiteListNotString_Failure($attr)
	{
		$this->assertSame($this->attrs, $this->attrs->addToWhiteList($attr));
	}

	/**
	 * @depends	testAddToWhiteListValidString
	 * @return	null
	 */
	public function testLoadWhiteListValidList()
	{
		$list = array(
			'attr-1',
			'attr-2',
			'attr-3',
			'attr-4'
		);
		$this->assertSame($this->attrs, $this->attrs->loadWhiteList($list));
		$this->assertEquals($list, $this->attrs->getWhiteList(false));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddToWhiteListValidString
	 * @dataProvider		provideEmptyStrings
	 * @return	null
	 */
	public function testLoadWhiteListEmptyString_Failure($attr)
	{
		$list = array(
			'attr-1',
			$attr,
			'attr-3',
			'attr-4'
		);
		$this->attrs->loadWhiteList($list);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testAddToWhiteListValidString
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return	null
	 */
	public function testLoadWhiteListNonString_Failure($attr)
	{
		$list = array(
			'attr-1',
			$attr,
			'attr-3',
			'attr-4'
		);
		$this->attrs->loadWhiteList($list);
	}

	/**	
	 * @depends	testInitialState
	 * @param	TagAttributes
	 * @return	null
	 */
	public function testAddWithGlobalAttrs(TagAttributes $attrs)
	{
		$globals = $attrs->getGlobalAttributes();
		$value   = 'i am a value';
		$expected = array();
		foreach ($globals as $attr) {
			$expected[$attr] = $value;
			$this->assertFalse($attrs->exists($attr));
			$this->assertSame($attrs, $attrs->add($attr, $value));
			$this->assertTrue($attrs->exists($attr));
			$this->assertEquals($value, $attrs->get($attr));
		}
	
		$this->assertEquals(count($expected), $attrs->count());
		$this->assertEquals($expected, $attrs->getAll());
		$this->assertSame($attrs, $attrs->clear());
		$this->assertEquals(array(), $attrs->getAll());
		$this->assertEquals(0, $attrs->count());
	}

	/**	
	 * @depends	testInitialState
	 * @param	TagAttributes
	 * @return	null
	 */
	public function testAddWithValidation(TagAttributes $attrs)
	{
		$attrs->addToWhiteList('my-attr-1')
			  ->addToWhiteList('my-attr-2');
		
		$value1 = 'my-value';
		$value2 = null;
		$this->assertSame($attrs, $attrs->add('my-attr-1', $value1));
		$this->assertEquals(1, $attrs->count());
		$this->assertTrue($attrs->exists('my-attr-1'));
		$this->assertEquals($value1, $attrs->get('my-attr-1'));


		$this->assertSame($attrs, $attrs->add('my-attr-2', $value2));
		$this->assertEquals(2, $attrs->count());
		$this->assertTrue($attrs->exists('my-attr-1'));
		$this->assertTrue($attrs->exists('my-attr-2'));
		$this->assertEquals($value1, $attrs->get('my-attr-1'));
		$this->assertEquals($value2, $attrs->get('my-attr-2'));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideEmptyStrings
	 * @param				string	$attr
	 * @return				null
	 */
	public function testAddFirstParamEmptyString_Failure($attr)
	{
		$this->attrs->add($attr, 'my-value');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @param				string	$attr
	 * @return				null
	 */
	public function testAddSecondParamNonString_Failure($value)
	{
		$this->attrs->add('id', $value);
	}

	/**
	 * @expectedException	RunTimeException
	 * @return				null
	 */
	public function testFailedValidation_Failure()
	{
		$this->attrs->addToWhiteList('my-attr-1');
		
		$this->attrs->add('my-attr-3', 'my-value');
	}

	/**
	 * @return	null
	 */
	public function testFailedValidationWhenDisabled()
	{
		$this->attrs->disableValidation();
		$this->attrs->addToWhiteList('my-attr-1');
		$this->assertSame(
			$this->attrs,
			$this->attrs->add('my-attr-3', 'my-value')
		);

		$this->assertEquals(1, $this->attrs->count());
		$this->assertTrue($this->attrs->exists('my-attr-3'));
		$this->assertEquals('my-value', $this->attrs->get('my-attr-3'));
	}
	
	/**
	 * @return	null
	 */
	public function testBuildSingle()
	{
		$this->attrs->add('id', '1234');
		$result = $this->attrs->build();
		$expected = 'id="1234"';
		$this->assertEquals($expected, $this->attrs->build());
	}

	/**
	 * @return	null
	 */
	public function testBuildSingleEnumerated()
	{
		$this->attrs->addToWhiteList('disabled');
		$this->attrs->add('disabled');

		$this->assertEquals('disabled', $this->attrs->build());

	}

	/**
	 * @return	null
	 */
	public function testBuildMany()
	{
		$this->attrs->disableValidation();
		$this->attrs->add('my-attr', 'my-value')
					->add('id', '9993')
					->add('class', 'my-class');

		$expected = 'my-attr="my-value" id="9993" class="my-class"';
		$this->assertEquals($expected, $this->attrs->build());
	}

	/**
	 * @return	null
	 */
	public function testBuildManyMixed()
	{
		$this->attrs->disableValidation();
		$this->attrs->add('my-attr', 'my-value')
					->add('id', '9993')
					->add('disabled')
					->add('class', 'my-class');

		$expected = 'my-attr="my-value" id="9993" disabled class="my-class"';
		$this->assertEquals($expected, $this->attrs->build());
	}

	/**
	 * @return	null
	 */
	public function testBuildEmpty()
	{
		$this->assertEquals(0, $this->attrs->count());
		$this->assertEquals('', $this->attrs->build());
	}

	public function testBuildToString()
	{
		$this->attrs->disableValidation();
		$this->attrs->add('my-attr', 'my-value')
					->add('id', '9993')
					->add('class', 'my-class');

		$expected = 'my-attr="my-value" id="9993" class="my-class"';

		$this->expectOutputString($expected);
		echo $this->attrs;
	}
}
