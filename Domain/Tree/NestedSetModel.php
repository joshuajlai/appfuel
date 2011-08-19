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
namespace Appfuel\Domain\Tree;

use Appfuel\Orm\Domain\DomainModel;

/**
 * Appfuel User domain model
 */
class NestedSetModel extends DomainModel
{
	/**
	 * ParentId is a link to this nodes parent
	 * @var int
	 */
	protected $nodeParentId = null;

	/**
	 * Text used to name the node
	 * @var string
	 */
	protected $nodeLabel = null;
	
	/**
	 * Describes what type of node this node is
	 * @var string
	 */
	protected $nodeType = null;
	
	/**
	 * Left value of this node
	 * @var int
	 */
	protected $leftNode = null;
	
	/**
	 * Right value of this node
	 * @var string
	 */
	protected $rightNode = null;
}
