<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Fuelcell\Action\Build;

use Exception,
	RunTimeException,
	Appfuel\Kernel\Mvc\MvcAction,
	Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Html\Resource\ResourceTreeWriter,
    Appfuel\Html\Resource\ResourceTreeBuilder;

/**
 * Build clientside intermediate tree
class BuildClientsideResourceTree extends MvcAction
{
	/**
	 * @param	MvcContextInterface $context
	 * @return	null
	 */
	public function process(MvcContextInterface $context)
	{
		$builder = new ResourceTreeBuilder();
		$tree = $builder->buildTree();

		$writer = new ResourceTreeWriter();
		$writer->writeTree($tree);

		$view = $context->getView();
		$view->assign('result', "tree built");
	}

}
