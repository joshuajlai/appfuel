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
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Html\Resource\ResourceFactory;

/**
 */
class ResourceTest extends BaseTestCase
{
	/**
	 * @test
	 * @return	Pkg
	 */
	public function createFactory()
	{
		$factory = new ResourceFactory();	
		$this->assertInstanceof(
			'Appfuel\Html\Resource\ResourceFactoryInterface', 
			$factory
		);

		return $factory;
	}

	/**
	 * @test
	 * @depends	createFactory
	 * @return	ResourceFactory
	 */
	public function createResourceAdapter(ResourceFactory $factory)
	{
		$adapter = $factory->createResourceAdapter();
		$this->assertInstanceOf(
			'Appfuel\Html\Resource\AppfuelAdapter',
			$adapter
		);

		return $factory;
	}

	/**
	 * @test
	 * @depends	createFactory
	 * @return	ResourceFactory
	 */
	public function createFileStack(ResourceFactory $factory)
	{
		$stack = $factory->createFileStack();
		$this->assertInstanceof(
			'Appfuel\Html\Resource\FileStack',
			$stack
		);

		return $factory;
	}

	/**
	 * @test
	 * @depends	createFactory
	 * @return	ResourceFactory
	 */
	public function createVendor(ResourceFactory $factory)
	{
		$data  = array(
			'name'      => 'my-vendor',
			'version'   => '1.0',
			'path'      => 'some/path',
			'layers'	=> array()
		);
		$vendor = $factory->createVendor($data);
		$this->assertInstanceof(
			'Appfuel\Html\Resource\Vendor',
			$vendor
		);

		return $factory;
	}

	/**
	 * @test
	 * @depends	createFactory
	 * @return	ResourceFactory
	 */
	public function createLayer(ResourceFactory $factory)
	{
		$name   = 'my-layer';
		$vendor = $this->getMock('Appfuel\Html\Resource\VendorInterface');
		$layer  = $factory->createLayer($name, $vendor);
		$this->assertInstanceof('Appfuel\Html\Resource\AppfuelLayer',$layer);

		return $factory;
	}

	/**
	 * @test
	 * @depends	createFactory
	 * @return	ResourceFactory
	 */
	public function createPkgPkg(ResourceFactory $factory)
	{
		$data  = array(
			'name'  => 'my-pkg',
			'type'  => 'pkg',
		);
		$vendor = 'appfuel';
		$pkg = $factory->createPkg($data, $vendor);
		$this->assertInstanceof('Appfuel\Html\Resource\Pkg', $pkg);

		return $factory;
	}

	/**
	 * @test
	 * @depends	createFactory
	 * @return	ResourceFactory
	 */
	public function createViewPkg(ResourceFactory $factory)
	{
		$data  = array(
			'name'   => 'my-pkg',
			'type'   => 'view',
			'markup' => 'my-markup.phtml',
		);
		$vendor = 'appfuel';
		$pkg = $factory->createPkg($data, $vendor);
		$this->assertInstanceof('Appfuel\Html\Resource\ViewPkg', $pkg);

		return $factory;
	}

	/**
	 * @test
	 * @depends	createFactory
	 * @return	ResourceFactory
	 */
	public function createPagePkg(ResourceFactory $factory)
	{
		$data  = array(
			'name'   => 'my-pkg',
			'type'   => 'page',
			'markup' => 'my-markup.phtml',
		);
		$vendor = 'appfuel';
		$pkg = $factory->createPkg($data, $vendor);
		$this->assertInstanceof('Appfuel\Html\Resource\PagePkg', $pkg);

		return $factory;
	}

	/**
	 * @test
	 * @depends	createFactory
	 * @return	ResourceFactory
	 */
	public function createHtmlDocPkg(ResourceFactory $factory)
	{
		$data  = array(
			'name'   => 'my-pkg',
			'type'   => 'htmldoc',
			'markup' => 'my-markup.phtml',
		);
		$vendor = 'appfuel';
		$pkg = $factory->createPkg($data, $vendor);
		$this->assertInstanceof('Appfuel\Html\Resource\HtmlDocPkg', $pkg);

		return $factory;
	}

	/**
	 * @test
	 * @depends	createFactory
	 * @return	ResourceFactory
	 */
	public function createUnkownPkg(ResourceFactory $factory)
	{
		$data  = array(
			'name'   => 'my-pkg',
			'type'   => 'unkown',
			'markup' => 'my-markup.phtml',
		);
		$vendor = 'appfuel';
		$this->setExpectedException('DomainException');
		$pkg = $factory->createPkg($data, $vendor);
		$this->assertInstanceof('Appfuel\Html\Resource\HtmlDocPkg', $pkg);

		return $factory;
	}

	/**
	 * @test
	 * @depends	createFactory
	 * @return	ResourceFactory
	 */
	public function createPkgName(ResourceFactory $factory)
	{
		$name    = "appfuel:pkg.my-package";
		$pkgName = $factory->createPkgName($name);
		$this->assertInstanceof('Appfuel\Html\Resource\PkgName', $pkgName);

		$name    = 'pkg.my-package';
		$pkgName = $factory->createPkgName($name, 'appfuel');
		$this->assertInstanceof('Appfuel\Html\Resource\PkgName', $pkgName);
		$this->assertEquals('appfuel', $pkgName->getVendor());	
		return $factory;
	}







}
