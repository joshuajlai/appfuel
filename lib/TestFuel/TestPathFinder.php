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
namespace TestFuel;

use Appfuel\Framework\File\PathFinder;

/**
 * Used for searching test paths 
 */
class TestPathFinder extends PathFinder
{
    /**
     * @return  ViewPathFinder
     */
    public function __construct()
    {
		parent::__construct();
		$this->setRelativeRootPath('test/files');
    }

}
