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

use Appfuel\Stdlib\Error\PHPError;

/**
 * 
 */
class PHPErrorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * System Under Test
	 * @var Appfuel\Stdlib\Error\Error
	 */
	protected $error = NULL;

	/**
	 * Error level as reported by php. Used to restore the level
	 * @var int
	 */
	protected $errLevel = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->errLevel = error_reporting();
		$this->error = new PHPError();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		error_reporting($this->errLevel);
		unset($this->error);
	}

    /**
     * The constructor adds any codes that use bit masks that can not be
     * hard coded as class parameters
     * @return void
     */
    public function testConstructorGetCodes()
    {
        $codes = $this->error->getCodes();

        /* we don't care what the codes are because they can change but
         * we care that there are codes in there
         */
        $this->assertInternalType('array', $codes);
        $this->assertFalse(empty($codes));

        /*
         * simple and all_strict are masks 
         */
        $this->assertContains('simple', $codes);
        $this->assertContains('all_strict', $codes);

        $simpleNbr =  E_ERROR | E_WARNING | E_PARSE;
        $this->assertEquals($simpleNbr, $codes['simple']);

        $allStrictNbr = E_ALL | E_STRICT;
        $this->assertEquals($allStrictNbr, $codes['all_strict']);
    }

    /**
     * Check that you can get each value from the code by getting
     * all the valid codes and using them
     *
     * @return void
     */
    public function testIsErrorCodeGetLevel()
    {
        $codes = $this->error->getCodes();
        foreach ($codes as $key => $value) {
            $this->assertTrue($this->error->isCode($key));
            $this->assertEquals($value, $this->error->getLevel($key));
        }

        $this->assertFalse($this->error->isCode('wouldNotExist'));
        $this->assertFalse($this->error->getLevel('wouldNotExist'));
    }

    /**
     * This tests the error contstants to see if we can get the code
     * 
     * @return void
     */
    public function testGetCode()
    {
        $codes = $this->error->getCodes();
        foreach ($codes as $code => $value) {
            $this->assertEquals($code, $this->error->getCode($value));
        }

        $this->assertFalse($this->error->getCode(99999999999));
    }

    /**
     * @return void
     */
    public function testGetReportingLevel()
    {
        $level = error_reporting();
        $code  = $this->error->getCode($level);

        $result = $this->error->getReportingLevel();
        $this->assertEquals($code, $result);

        $raw = TRUE;
        $result = $this->error->getReportingLevel($raw);
        $this->assertEquals($level, $result);
    }

    /**
     * Test the ability to set the error reporting level with
     * both code and actual constant
     *
     * @return void
     */
    public function testSetReportingLevel()
    {
        $codes = $this->error->getCodes();
        foreach ($codes as $code => $value) {
            $oldLevel = error_reporting();

            /* should return old level */
            $result = $this->error->setReportingLevel($code);
            $this->assertEquals($oldLevel, $result);

            /* the current reporting should now be set to value */
            $this->assertEquals($value, error_reporting());
        }

        /* test set the reporting level with constant not the code */
        foreach ($codes as $code => $value) {
            $oldLevel = error_reporting();

            /* should return old level */
            $result = $this->error->setReportingLevel($value, TRUE);
            $this->assertEquals($oldLevel, $result);

            /* the current reporting should now be set to value */
            $this->assertEquals($value, error_reporting());
        }
    }

    /**
     * @return void
     */
    public function testGetSetDisplayError()
    {
        /* we don't care so much about the code accept they exist */
        $codes = $this->error->getDisplayCodes();
        $this->assertInternalType('array', $codes);
        $this->assertFalse(empty($codes));

        /* test getDisplayStatus by manually changing the status to known
         * valuse
         */
        foreach ($codes as $code => $value) {
            ini_set('display_errors', $value);
            $result = $this->error->getDisplayStatus();
            $this->assertEquals($value, $result);
        }

        /*
         * setDisplayStatus should return the old status
         */
        foreach ($codes as $code => $value) {
            $oldValue = ini_get('display_errors');
            $result = $this->error->setDisplayStatus($code);
            $this->assertEquals($oldValue, $result);

            $expected = ini_get('display_errors');
            $result = $this->error->getDisplayStatus();
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @return void
     */
    public function testEnableDisplay()
    {
        /* enable display returns the old display status */
        $expected = ini_get('display_errors');
        $result   = $this->error->enableDisplay();
        $this->assertEquals($expected, $result);

        $expected = "1";
        $result = $this->error->getDisplayStatus();
        $this->assertEquals($expected, $result);
    }

    /**
     * @return void
     */
    public function testDisableDisplay()
    {
        /* enable display returns the old display status */
        $expected = ini_get('display_errors');
        $result   = $this->error->disableDisplay();
        $this->assertEquals($expected, $result);

        $expected = "0";
        $result = $this->error->getDisplayStatus();
        $this->assertEquals($expected, $result);
    }

    /**
     * @return void
     */
    public function testSendToStdErr()
    {
        /* enable display returns the old display status */
        $expected = ini_get('display_errors');
        $result   = $this->error->sendToStdErr();
        $this->assertEquals($expected, $result);

        $expected = "stderr";
        $result = $this->error->getDisplayStatus();
        $this->assertEquals($expected, $result);
    }
}

