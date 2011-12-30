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
namespace Appfuel\View\Html\Tag;

use RunTimeException;

/**
 */
class HmltTag extends GenericTag
{
	protected $head = null;

	protected $body = null;

	/**
	 * Only has two valid attributes href and target
	 *
	 * @return	base
	 */
	public function __construct()
	{
		parent::__construct('html');
	}
}
