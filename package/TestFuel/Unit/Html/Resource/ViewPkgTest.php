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
	Appfuel\Html\Resource\ViewPkg,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class ViewPkgTest extends BaseTestCase
{
	/**
	 * @var array
	 */
	protected $pkgData = array(
		'name'   => 'my-package',
		'desc'   => 'A example package for testing does not really exist',
		'type'   => 'view',
		'path'   => 'view/myview',
		'markup' => 'markup.phtml', 
		'src'  => array(
			'files' => array(
				'js'	=> array('js/file_a.js', 'js/file_b.js'),
				'css'	=> array('css/file_c.css', 'js/file_d.css'),
			),
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
	public function createViewPkg()
	{
		$data   = $this->getPkgData();
		$vendor = 'appfuel';
		$pkg  = new ViewPkg($data, $vendor);
		
		$this->assertInstanceof('Appfuel\Html\Resource\PkgInterface', $pkg);
		$this->assertInstanceof('Appfuel\Html\Resource\ViewPkgInterface', $pkg);
	
		$this->assertFalse($pkg->isJsView());	
		$srcPath  = $data['path'] . '/src';
		$expected = $srcPath . '/' . $data['markup'];
		$this->assertEquals($expected, $pkg->getMarkupFile()); 
		return $pkg;
	}
}
