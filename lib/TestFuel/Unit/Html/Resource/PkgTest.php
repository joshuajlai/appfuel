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
namespace TestFuel\Unit\Html\Resource;

use StdClass,
	Appfuel\Html\Resource\Pkg,
	TestFuel\TestCase\BaseTestCase;

/**
 * The package manifest holds meta data, file list, tests and dependencies
 * for the package
 */
class PkgTest extends BaseTestCase
{
	/**
	 * @var array
	 */
	protected $pkgData = array(
		'name' => 'my-package',
		'desc' => 'A example package for testing does not really exist',
		'type' => 'pkg',
		'path' => 'some/path/to/myPackage',
		'src'  => array(
			'dir' => 'altDir',
			'files' => array(
				'js'	=> array('js/file_a.js', 'js/file_b.js'),
				'css'	=> array('css/file_c.css', 'js/file_d.css'),
			),
		),
		'require' => array("appfuel:pkg.kernel", "mvc", "yui3:test"),
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
	public function createPkg()
	{
		$data   = $this->getPkgData();
		$vendor = 'appfuel';
		$pkg  = new Pkg($data, $vendor);
		
		$this->assertInstanceof('Appfuel\Html\Resource\PkgInterface', $pkg);
		$this->assertEquals($data['name'], $pkg->getName());
		$this->assertEquals($data['type'], $pkg->getType());
		$this->assertEquals($data['desc'], $pkg->getDescription());
		$this->assertEquals($data['path'], $pkg->getPath());
		$this->assertEquals($data['src']['dir'], $pkg->getSourceDirectory());
	
		$srcPath = $data['path'] . '/' . $data['src']['dir'];
		$this->assertEquals($srcPath, $pkg->getSourcePath());
		
		$js = $data['src']['files']['js'];
		foreach ($js as $idx =>&$file) {
			$file = "$srcPath/$file";
		}
		$this->assertEquals($js, $pkg->getFiles('js'));

		$css = $data['src']['files']['css'];
		foreach ($css as $idx =>&$file) {
			$file = "$srcPath/$file";
		}
		$this->assertEquals($css, $pkg->getFiles('css'));

		$this->assertTrue($pkg->isRequiredPackages());
		
		$list = $pkg->getRequiredPackages();
		$this->assertInternalType('array', $list);
		$this->assertEquals(3, count($list));
		return $pkg;
	}
}
