<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Html\Resource;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Html\Resource\HtmlDocPkg;

/**
 */
class HtmlDocPkgTest extends BaseTestCase
{
	/**
	 * @var array
	 */
	protected $pkgData = array(
		'name'   => 'fancy-html',
		'desc'   => 'A fancy html doc configuration',
		'type'   => 'htmldoc',
		'path'   => 'htmldoc',
		'markup' => 'fancydoc.phtml',
		'html'   => array(
			'attrs' => array('lang' => 'en'),
			'head'  => array(
				'title' => array('my title', ' '),
				'meta'  => array(
					array('name' => 'author', 'content' => 'robert')
				),
				'links' => array('some.css')
			)
		),
	);

	/**
	 * @return	array
	 */
	public function getPkgData()
	{
		return $this->pkgData;
	}

	/**
	 * @test
	 * @return	Pkg
	 */
	public function createHtmlDocPkg()
	{
		$data   = $this->getPkgData();
		$vendor = 'appfuel';
		$pkg  = new HtmlDocPkg($data, $vendor);
		$this->assertInstanceof('Appfuel\Html\Resource\PkgInterface', $pkg);
		$this->assertInstanceof(
			'Appfuel\Html\Resource\HtmlDocPkgInterface', 
			$pkg
		);
	
		$srcPath  = $data['path'] . '/src';
		$expected = $srcPath . '/' . $data['markup'];
		$this->assertEquals($expected, $pkg->getMarkupFile());

		$this->assertEquals($data['html'], $pkg->getHtmlConfig());
		return $pkg;
	}
}
