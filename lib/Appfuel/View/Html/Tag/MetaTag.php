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

/**
 * Currently I am not validating on what attributes exist with other attributes
 */
class MetaTag extends HtmlTag
{
	/**
	 * Only has two valid attributes href and target
	 *
	 * @return	base
	 */
	public function __construct($name = null, $content = null, $equiv = null)
	{
		parent::__construct('meta');
		$attrs = $this->getTagAttributes();
		$attrs->loadWhiteList(array('charset','content','http-equiv','name'));
			  
		
		if (null !== $equiv) {
			$attrs->add('http-equiv', $equiv);
		}

		if (null !== $name) {
			$attrs->add('name', $name);
		}

		if (null !== $content) {
			$attrs->add('content', $content);
		}

			 
		$this->disableClosingTag();
	}
}
