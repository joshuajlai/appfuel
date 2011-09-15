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
namespace Appfuel\View;

use Appfuel\Framework\File\PathFinder;

/**
 * This setup the relative root path under the base path. So that anyone
 * using this finder will can specify a relative path that will be searched
 * in AF_BASE_PATH/ui/appfuel
 */
class ViewPathFinder extends PathFinder
{
    /**
     * @return  ViewPathFinder
     */
    public function __construct()
    {
		parent::__construct();
		$this->setRelativeRootPath('ui/appfuel');
    }

}
