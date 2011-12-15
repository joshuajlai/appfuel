<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\View\Html\Compositor;

use Appfuel\Kernel\PathFinder,
	Appfuel\Kernel\PathFinderInterface,
	Appfuel\View\Compositor\FileCompositor;

/**
 * Assign a path finder with a relative path of ui/appfuel/html
 */
class HtmlCompositor extends FileCompositor implements HtmlCompositorInterface
{
    /**
     * @param   array   $data
     * @return  Template
     */
    public function __construct(PathFinderInterface $pathFinder = null)
    {
		if (null === $pathFinder) {
			$pathFinder = new PathFinder('ui/appfuel/html');
		}
		parent::__construct($pathFinder);
    }
}
