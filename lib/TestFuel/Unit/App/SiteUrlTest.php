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
namespace TestFuel\Unit\View;

use StdClass,
	SplFileInfo,
	Appfuel\App\SiteUrl,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class SiteUrlTest extends BaseTestCase
{

	/**
	 * @return	null
	 */
	public function testOnlyRequiredParams()
	{
		$base = 'someurl.com';
		$dir  = 'resources';
		$vendor = 'appfuel';

		$url = new SiteUrl($base, $dir, $vendor);
		$this->assertInstanceOf(
			'Appfuel\App\SiteUrlInterface',
			$url
		);
		$this->assertEquals($base, $url->getBase());
		$this->assertEquals($dir, $url->getResourceDir());
		$this->assertEquals($vendor, $url->getVendor());
		$this->assertEquals('', $url->getVersion());
		$this->assertEquals('', $url->getRelativeRoot());
		$this->assertEquals('http', $url->getScheme());
	}

	/**
	 * @dataProvider	provideNonEmptyStringsNoNumbers
	 * @return			null
	 */
	public function testSetBaseNonEmpty($base)
	{
		$site = new SiteUrl($base, 'somedir', 'somevendor');
		$this->assertEquals($base, $site->getBase());
	}

	/**
	 * @dataProvider	provideEmptyStrings
	 * @return			null
	 */
	public function testSetBaseEmptyString($base)
	{
		$site = new SiteUrl($base, 'somedir', 'somevendor');
		$this->assertEquals(trim($base), $site->getBase());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetBaseNotString_Failure($base)
	{
		$site = new SiteUrl($base, 'somedir', 'somevendor');
	}

	/**
	 * @dataProvider	provideNonEmptyStringsNoNumbers
	 * @return			null
	 */
	public function testSetResourceDirNonEmpty($dir)
	{
		$site = new SiteUrl('somebase.com', $dir, 'somevendor');
		$this->assertEquals($dir, $site->getResourceDir());
	}

	/**
	 * @dataProvider	provideEmptyStrings
	 * @return			null
	 */
	public function testSetResourceDirEmpty($dir)
	{
		$site = new SiteUrl('somebase.com', $dir, 'somevendor');
		$this->assertEquals(trim($dir), $site->getResourceDir());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetResourceDirNotString_Failure($dir)
	{
		$site = new SiteUrl('somebase.com', $dir, 'somevendor');
	}

	/**
	 * @dataProvider	provideNonEmptyStringsNoNumbers
	 * @return			null
	 */
	public function testSetVendorNonEmpty($name)
	{
		$site = new SiteUrl('somebase.com','dir',  $name);
		$this->assertEquals($name, $site->getVendor());
	}

	/**
	 * @dataProvider	provideEmptyStrings
	 * @return			null
	 */
	public function testSetVendorEmptyStrings($name)
	{
		$site = new SiteUrl('somebase.com','dir',  $name);
		$this->assertEquals(trim($name), $site->getVendor());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetVendorNotString_Failure($name)
	{
		$site = new SiteUrl('somebase.com', 'dir', $name);
	}

	/**
	 * This data provider includes numbers like 1 and 1.0
	 * @dataProvider	provideNonEmptyStrings
	 * @return			null
	 */
	public function testSetVersion($version)
	{
		$site = new SiteUrl('somebase.com','dir', 'vendor', $version);
		$this->assertEquals((string)$version, $site->getVersion());
	}

	/**
	 * @dataProvider	provideEmptyStrings
	 * @return			null
	 */
	public function testSetVersionEmptyStrings($version)
	{
		$site = new SiteUrl('somebase.com','dir', 'vendor', $version);
		$this->assertEquals(trim($version), $site->getVersion());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideNoCastableStrings
	 * @return				null
	 */
	public function testSetVersionInvalidStrings_Failure($version)
	{
		$site = new SiteUrl('somebase.com','dir', 'vendor', $version);
	}

	/**
	 * @dataProvider	provideNonEmptyStrings
	 * @return			null
	 */
	public function testSetRelativeRoot($path)
	{
		$site = new SiteUrl('somebase.com','dir', 'vendor', '1.0', $path);
		$this->assertEquals($path, $site->getRelativeRoot());
	}

	/**
	 * @dataProvider	provideEmptyStrings
	 * @return			null
	 */
	public function testSetRelativeRootEmptyStrings($path)
	{
		$site = new SiteUrl('somebase.com','dir', 'vendor', '1.0', $path);
		$this->assertEquals(trim($path), $site->getRelativeRoot());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetRelativeRootInvalidStrings_Failure($path)
	{
		$site = new SiteUrl('somebase.com','dir', 'vendor', '1.0', $path);
	}

	/**
	 * @return	null
	 */
	public function testSetHttps()
	{
		$site = new SiteUrl('url', 'dir', 'vendor', '1.0', 'path', true);
		$this->assertEquals('https', $site->getScheme());

		$site = new SiteUrl('url', 'dir', 'vendor', '1.0', 'path', false);
		$this->assertEquals('http', $site->getScheme());

		/* only a strict true will toggle https */
		$site = new SiteUrl('url', 'dir', 'vendor', '1.0', 'path', 'true');
		$this->assertEquals('http', $site->getScheme());

		$site = new SiteUrl('url', 'dir', 'vendor', '1.0', 'path', 1);
		$this->assertEquals('http', $site->getScheme());
	}

	/**
	 * @return	null
	 */
	public function testGetUrlAllParams()
	{
		$site = new SiteUrl(
			'mysite.com', 
			'resources', 
			'yui3', 
			'3.4.1',
			'build',
			true
		);

		$path = "loader/yui-loader-min.js";
		$expected = "https://mysite.com/resources/yui3/3.4.1/build/$path";
		
		$this->assertEquals($expected, $site->getUrl($path));
		
		$expected = "resources/yui3/3.4.1/build/$path";
		$this->assertEquals($expected, $site->getUrl($path, false));

		$site = new SiteUrl(
			'mysite.com', 
			'resources', 
			'yui3', 
			'3.4.1',
			'build'
		);

		$expected = "http://mysite.com/resources/yui3/3.4.1/build/$path";
		$this->assertEquals($expected, $site->getUrl($path, true));

		$expected = "resources/yui3/3.4.1/build/$path";
		$this->assertEquals($expected, $site->getUrl($path, false));
	}

	/**
	 * @return	null
	 */
	public function testGetUrlNoRelativeRoot()
	{
		$site = new SiteUrl('mysite.com','resources', 'yui3', '3.4.1');
		
		$path = "loader/yui-loader-min.js";
		$expected = "http://mysite.com/resources/yui3/3.4.1/$path";
		$this->assertEquals($expected, $site->getUrl($path));
		
		$expected = "resources/yui3/3.4.1/$path";
		$this->assertEquals($expected, $site->getUrl($path, false));
	}

	/**
	 * @return	null
	 */
	public function testGetUrlNoVersionNoRelativeRoot()
	{
		$site = new SiteUrl('mysite.com','resources', 'yui3');
		$path = "loader/yui-loader-min.js";
		$expected = "http://mysite.com/resources/yui3/$path";
		$this->assertEquals($expected, $site->getUrl($path));
		

		$expected = "resources/yui3/$path";
		$this->assertEquals($expected, $site->getUrl($path, false));
	}

	/**
	 * @return	null
	 */
	public function testGetUrlNoVendorNoVersionNoRelativeRoot()
	{
		$site = new SiteUrl('mysite.com','resources', '');
		$path = "loader/yui-loader-min.js";
		$expected = "http://mysite.com/resources/$path";
		$this->assertEquals($expected, $site->getUrl($path));
		
		$expected = "resources/$path";
		$this->assertEquals($expected, $site->getUrl($path, false));
	}

	/**
	 * @return	null
	 */
	public function testGetUrlNoResourceDirNoVendorNoVersionNoRelativeRoot()
	{
		$site = new SiteUrl('mysite.com','', '');
		$path = "loader/yui-loader-min.js";
		$expected = "http://mysite.com/$path";
		$this->assertEquals($expected, $site->getUrl($path));
		
		$this->assertEquals($path, $site->getUrl($path, false));
	}

	/**
	 * @return	null
	 */
	public function testGetUrlAllEmpty()
	{
		$site = new SiteUrl('','', '');
		$path = "/loader/yui-loader-min.js";
		$this->assertEquals($path, $site->getUrl($path));
		$this->assertEquals($path, $site->getUrl($path, false));
		

		$path = "loader/yui-loader-min.js";
		$this->assertEquals($path, $site->getUrl($path));
		
		$this->assertEquals($path, $site->getUrl($path, false));
	}



}
