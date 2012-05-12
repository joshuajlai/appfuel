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
namespace TestFuel\Unit\Filesystem;

use StdClass,
	SplFileInfo,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileFinderInterface,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class FileFinderTest extends BaseTestCase
{
	/**
	 * Interface used to test against the FileFinder
	 * @param	string
	 */
	protected $finderInterface = 'Appfuel\Filesystem\FileFinderInterface';

	/**
	 * @return	array
	 */
	public function provideNonStrings()
	{
		return array(
			array(1),
			array(-1),
			array(0),
			array(array(1,2,3)),
			array(new StdClass()),
			array(true),
			array(false),
		);
	}

	/**
	 * @return	string
	 */
	public function getFinderInterface()
	{
		return $this->finderInterface;
	}

	/**
	 * This is required by the framework not just this module. The FileFinder
	 * can work without this constant but since I have no way of dynamically
	 * removing the constant I don't bother
	 *
	 * @test
	 * @return	null
	 */
	public function basePathIsDefined()
	{
		$this->assertTrue(defined('AF_BASE_PATH'));
	}
	
	/**
	 * @test
	 * @depends	basePathIsDefined
	 * @return	FileFinder
	 */
	public function ensureInterfaceIsImplemented()
	{
		$finder = new FileFinder();
		$this->assertInstanceOf($this->getFinderInterface(), $finder);

		return $finder;
	}

	/**
	 * When created with no parameters: base path is enabled and root path
	 * is an empty string. This finder can build absolulte paths for anything
	 * below the applications base path
	 *
	 * @test
	 * @depends	ensureInterfaceIsImplemented
	 * @return	FileFinder
	 */
	public function defaultFinder(FileFinderInterface $finder)
	{
		$this->assertTrue($finder->isBasePath());
		$this->assertEquals(AF_BASE_PATH, $finder->getBasePath());
		$this->assertEquals('', $finder->getRootPath());

		return $finder;
	}

	/**
	 * Since the default is for the 2nd param is true this scenerio will 
	 * be used rarely if ever
	 *
	 * @test
	 * @depends	ensureInterfaceIsImplemented
	 */
	public function noRootWithBasePath()
	{
		$finder = new FileFinder(null, true);
		$this->assertTrue($finder->isBasePath());
		$this->assertEquals(AF_BASE_PATH, $finder->getBasePath());
		$this->assertEquals('', $finder->getRootPath());
	}

	/**
	 * When created with the first param as a path, then the finder will
	 * create absolute paths for any paths below the root path. The absolute
	 * path will still include base path.
	 *
	 * @test
	 * @depends	ensureInterfaceIsImplemented
	 * @return FileFinder
	 */
	public function defaultFinderWithRootPath()
	{
		$root = 'test/resource/testfuel';
		$finder = new FileFinder($root);
		$this->assertTrue($finder->isBasePath());
		$this->assertEquals(AF_BASE_PATH, $finder->getBasePath());
		$this->assertEquals($root, $finder->getRootPath());	

		return $finder;
	}

	/**
	 * @test
	 * @depends	ensureInterfaceIsImplemented
	 * @return	FileFinder
	 */	
	public function rootPathNoBasePath()
	{
		$root = '/usr/local/share/pear';
		$finder = new fileFinder($root, false);
		$this->assertFalse($finder->isBasePath());
		$this->assertNull($finder->getBasePath());
		$this->assertEquals($root, $finder->getRootPath());

		return $finder;	
	}

	/**
	 * @test
	 * @depends	ensureInterfaceIsImplemented
	 * @return	FileFinder
	 */	
	public function noRootPathNoBasePath()
	{
		$finder = new fileFinder(null, false);
		$this->assertFalse($finder->isBasePath());
		$this->assertNull($finder->getBasePath());
		$this->assertEquals('', $finder->getRootPath());

		return $finder;	
	}


	/**
	 * The default finder has base path enabled which means when we 
	 * set the root path it is always relative to the base path. Because
	 * of this an empty string and '/' mean the same thing in this context
	 *
	 * @test
	 * @depends defaultFinder
	 * @return	null
	 */
	public function basicRootPathSetter(FileFinderInterface $finder)
	{
		$root = 'test/resource/testfuel';
		$this->assertSame($finder, $finder->setRootPath($root));
		$this->assertEquals($root, $finder->getRootPath());

		$root = '';
		$this->assertSame($finder, $finder->setRootPath($root));
		$this->assertEquals($root, $finder->getRootPath());

		$root = '/';
		$this->assertSame($finder, $finder->setRootPath($root));
		$this->assertEquals($root, $finder->getRootPath());

		/*
		 * path separators that may conflict with other paths
		 * are resolved with getPath when you get the root path
		 * it is kept exactly how it was set
		 */
		$root = 'test/resource/testfuel/';
		$this->assertSame($finder, $finder->setRootPath($root));
		$this->assertEquals($root, $finder->getRootPath());
	}

	/**
	 * @test
	 * @depends	rootPathNoBasePath
	 * @return	null
	 */
	public function rootPathSetterBasePathDisabled(FileFinderInterface $finder)
	{
		$root = '/usr/local/share/pear';
		$this->assertSame($finder, $finder->setRootPath($root));
		$this->assertEquals($root, $finder->getRootPath());
	
		$root = '/';
		$this->assertSame($finder, $finder->setRootPath($root));
		$this->assertEquals($root, $finder->getRootPath());

		/* appfuel will make no assumption about your root path
		 * actually being absolute. 
		 */
		$root = 'some/path';
		$this->assertSame($finder, $finder->setRootPath($root));
		$this->assertEquals($root, $finder->getRootPath());
	}

	/**
	 * @test
	 * @depends	noRootPathNoBasePath
	 * @return	null
	 */
	public function rootPathEmptyWhenNoBasePath(FileFinderInterface $finder)
	{
		$this->setExpectedException('DomainException');
		$finder->setRootPath('');
	}

	/**
	 * @test
	 * @dataProvider		provideNonStrings
	 * @depends				ensureInterfaceIsImplemented
	 * @return				null
	 */
	public function rootPathSettingFailures($path)
	{
		$finder = new FileFinder();
		$this->setExpectedException('DomainException');
		$finder->setRootPath($path);
	}

	/**
	 * @test
	 * @depends defaultFinder
	 * @return	null
	 */
	public function rootPathContainsBasePathFailure(FileFinderInterface $finder)
	{
		$root = AF_BASE_PATH . '/test/resource/testfuel';
		$this->setExpectedException('DomainException');
		$finder->setRootPath($root);
	}

	/**
	 * @test
	 * @depends defaultFinder
	 * @return	null
	 */
	public function rootPathSetterWithObject(FileFinderInterface $finder)
	{
		$root = 'my/path';
		$path = new SplFileInfo($root);
		$this->assertSame($finder, $finder->setRootPath($path));
		$this->assertEquals($root, $finder->getRootPath());

		$finder->setRootPath('');
	}

	/**
	 * @test
	 * @depends	defaultFinder
	 * @return	null
	 */
	public function getPathUsingBasePath(FileFinderInterface $finder)
	{
		/*
		 * with no parameters get base will return the base url
		 */
		$this->assertEquals($this->getBasePath(), $finder->getPath());

		$relative = 'my/path';
		$expected = AF_BASE_PATH . '/' . $relative;
		$this->assertEquals($expected, $finder->getPath($relative));

		/* leading directory separator is ignored */
		$relative = '/my/path';
		$this->assertEquals($expected, $finder->getPath($relative));

		/* trailing directory separator is not ignored */
		$relative  = '/my/path/';
		$expected .= '/';
		$this->assertEquals($expected, $finder->getPath($relative));

		$relative = new SplFileInfo('my/path');
		$expected = AF_BASE_PATH . '/' . $relative;
		$this->assertEquals($expected, $finder->getPath($relative));
	}

	/**
	 * @test
	 * @depends	noRootPathNoBasePath
	 * @return	null
	 */
	public function getPathNotUsingBasePath(FileFinderInterface $finder)
	{
		$path = '/user/local/share/pear';
		$this->assertSame($path, $finder->getPath($path));
		$this->assertSame($path, $finder->getPath($path, false));
		
		$file = new SplFileInfo($path);
		$this->assertSame($path, $finder->getPath($file));
		$this->assertSame($path, $finder->getPath($path, false));

		$path = '/';
		$this->assertSame($path, $finder->getPath($path));
		$this->assertSame($path, $finder->getPath($path, false));

		$this->assertEquals('', $finder->getPath(''));
	}

	/**
	 * Here we use a file that is known to exist. At the following location:
	 * <base-path>/test/resource/testfuel/test-file.txt is a file added to 
	 * so that we could test file level operation. 
	 *
	 * @test
	 * @return	null
	 */
	public function fileExistsBasePath()
	{
		$root = 'test/resource/testfuel';
		$finder = new FileFinder($root);
		$this->assertTrue($finder->fileExists('test-file.txt'));
		$this->assertFalse($finder->fileExists('does-not-exist'));

		$absolute = $this->getBasePath() . '/' . $root . '/test-file.txt';
		$this->assertTrue($finder->fileExists($absolute, false));
	}

	/**
	 * Here we use a known directory to test the isDir call. The directory is
	 * located at: <base-path>/test/resource/testfuel
	 *
	 * @test
	 * @return	null
	 */
	public function isDirBasePath()
	{
		$root = 'test/resource/testfuel';
		$finder = new FileFinder($root);
		$this->assertTrue($finder->isDir());
		$this->assertFalse($finder->isDir('does-not-exist'));

		/* exists but is not a directory */
		$this->assertFalse($finder->isDir('test-file.txt'));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isReadableBasePath()
	{
		$root = 'test/resource/testfuel';
		$finder = new FileFinder($root);
		$this->assertTrue($finder->isReadable('test-file.txt'));
		$this->assertFalse($finder->isReadable('does-not-exist'));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isFileBasePath()
	{
		$root = 'test/resource/testfuel';
		$finder = new FileFinder($root);
		$this->assertTrue($finder->isFile('test-file.txt'));
		$this->assertFalse($finder->isFile());
	}



}
