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
 * The base tag specifies a default url and/or a default target for all 
 * elements with a url(hyperlinks, images, forms, etc..). This tag must
 * live in the head element but thats not enforced. 
 */
class BaseTag extends HtmlTag
{
	/**
	 * Only has two valid attributes href and target
	 *
	 * @return	base
	 */
	public function __construct($href = null, $target = null)
	{
		parent::__construct('base');
		$this->disableClosingTag();

		$attrs = $this->getTagAttributes();
		$attrs->loadWhiteList(array('href','target'));

		if (null === $href && null === $target) {
			$err = 'both params href and target can not be empty';
			throw new RunTimeException($err);			
		}
		
		if (is_string($href) && ! empty($href)) {
			$attrs->add('href', $href);
		}

		if (is_string($target) && ! empty($target)) {
			$attrs->add('target', $target);
		}
	}
}
