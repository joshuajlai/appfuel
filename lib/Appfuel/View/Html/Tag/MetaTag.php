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
class MetaTag extends GenericTag
{
	/**
	 * Only has two valid attributes href and target
	 *
	 * @param	string	$name
	 * @param	string	$content
	 * @param	string	$httpEquiv
	 * @param	string	$charset
	 * @return	MetaTag
	 */
	public function __construct($name = null, 
								$content = null, 
								$httpEquiv = null,
								$charset = null)
	{
		parent::__construct('meta');
		$attrs = $this->getTagAttributes();
		$attrs->loadWhiteList(array('charset','content','http-equiv','name'));
			  
		
		if (null !== $httpEquiv) {
			$attrs->add('http-equiv', $httpEquiv);
		}

		if (null !== $name) {
			$attrs->add('name', $name);
		}

		if (null !== $content) {
			$attrs->add('content', $content);
		}

		if (null !== $charset) {
			$attrs->add('charset', $charset);
		}

		$this->disableClosingTag();
	}
}
