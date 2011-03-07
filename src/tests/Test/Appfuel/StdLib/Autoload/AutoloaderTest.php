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
namespace Test\Appfuel\Stdlib\Autoload;

use Appfuel\Stdlib\Autoload\Autoloader;

/**
 * The autoloader wraps the php spl_autoload_* functionality
 */
class TestAutoloader extends \PHPUnit_Framework_TestCase
{
	/**
	 * System Under Test
	 * @var Appfuel\Stdlib\Autoload\Autoloader
	 */
	protected $loader = NULL;

	/**
	 * @return NULL
	 */
	public function setUp()
	{
		$this->loader = new Autoloader();
	}

	/**
	 * @return NULL
	 */
	public function tearDown()
	{
		unset($this->loader);
	}

    /**
     * @return NULL
     */
    public function testGetRegistered()
    {
        $expected = spl_autoload_functions();
        $this->assertEquals(
            $expected,
            $this->loader->getRegistered()
        );
    }

    /**
     * @return viod
     */
    public function testBackupGetBackup()
    {
        $this->assertNull(
            $this->loader->getBackup(),
            'Initial value of getBackup should be NULL'
        );

        $this->assertNull(
            $this->loader->backupRegistered(),
            'This method returns no values'
        );

        $expected = $this->loader->getRegistered();
        $this->assertEquals(
            $expected,
            $this->loader->getBackup(),
            'should be equal to the registered autoloaders'
        );
    }

    /**
     * Prove we can clear and restore backup autoloaders
     * @return void
     */
    public function testClearLoadersRestore()
    {
        $registered = spl_autoload_functions();
        $backup  = TRUE;
        $cleared = $this->loader->clearAutoloaders($backup);

        $currentLoaders = spl_autoload_functions();

        /* needed to keep phpunit working */
        spl_autoload_register('phpunit_autoload');

        $this->assertEquals(
            $registered,
            $cleared,
            'should return the autoloaders it cleared'
        );

        $this->assertInternalType('array', $currentLoaders);
        $this->assertTrue(empty($currentLoaders));

        /* remove to maintain state */
        spl_autoload_unregister('phpunit_autoload');

        $restored = $this->loader->restoreAutoloaders();
        $this->assertEquals(
            $cleared,
            $restored,
            'the restored autoloaders should be the same a the cleared'
        );

        $currentLoaders = spl_autoload_functions();
        $this->assertInternalType('array', $currentLoaders);
        $this->assertFalse(empty($currentLoaders));
        
		$this->assertEquals(
            $registered,
            $currentLoaders,
            'current loaders should equal the first loaders we saved'
        );
    }

    /**
     * When you clear the autoloaders without backup and try to restore 
     * them nothing happens because there is nothing to restore.
     *
     * @return NULL
     */
    public function testClearLoadersRestoreNoBackup()
    {
        $registered = spl_autoload_functions();
        $backup  = FALSE;
        $cleared = $this->loader->clearAutoloaders($backup);

        $currentLoaders = spl_autoload_functions();

        /* needed to keep phpunit working */
        spl_autoload_register('phpunit_autoload');


        $this->assertEquals(
            $registered,
            $cleared,
            'should return the autoloaders it cleared'
        );

        $this->assertInternalType('array', $currentLoaders);
        $this->assertTrue(empty($currentLoaders));

        /* remove to maintain state */
        spl_autoload_unregister('phpunit_autoload');

        $restored       = $this->loader->restoreAutoloaders();
        $currentLoaders = spl_autoload_functions();

        /* needed to keep phpunit working */
        spl_autoload_register('phpunit_autoload');
        $this->assertFalse(
            $restored,
            'Because nothing was backed up nothing should be restored'
        );
        $this->assertInternalType('array', $currentLoaders);
        $this->assertTrue(empty($currentLoaders));

        /* remove to maintain state */
        spl_autoload_unregister('phpunit_autoload');

        /* return previous autoloader state */
        foreach ($registered as $loader) {
            if (is_string($loader)) {
                spl_autoload_register($loader);
            } else if (is_array($loader) && 2 === count($loader)) {
                spl_autoload_register(array($loader[0], $loader[1]));
            }
        }
    }

    /**
     * @return NULL
     */
    public function testRegisterUnRegister()
    {
        $this->loader->clearAutoloaders();
        $result  = $this->loader->register();
        $loaders = spl_autoload_functions();

        /* needed to keep the tests working */
        spl_autoload_register('phpunit_autoload');
        $this->assertTrue($result);
        $this->assertInternalType('array', $loaders);
        $this->assertEquals(1, count($loaders));
        $this->assertInstanceOf(
            '\Appfuel\Stdlib\Autoload\Autoloader',
            $loaders[0][0]
        );
        $this->assertEquals('loadClass', $loaders[0][1]);

        /* no unregister the registered autoloader */
        $result = $this->loader->unregister();
        $this->assertTrue($result);
        $loaders = spl_autoload_functions();

        $this->assertInternalType('array', $loaders);
        $this->assertEquals(1, count($loaders));
        $this->assertEquals(
            'phpunit_autoload',
            $loaders[0],
            'should just be phpunit left'
        );
        spl_autoload_unregister('phpunit_autoload');

        $this->loader->restoreAutoloaders();
    }
}

