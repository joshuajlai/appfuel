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
 * Html anchor tag
 */
class AnchorTag extends GenericTag
{
	/**
	 * @param	string	$href	url or file path to resource
	 * @param	string	$rel	relationship between current doc and link
	 * @param	string  $type	mime type
	 * @return	LinkTag
	 */
	public function __construct($href, $content = null)
	{
		if (empty($href) || ! is_string($href)) {
			$err = 'href must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		parent::__construct('a');

		$attrs = $this->getTagAttributes();
		$attrs->loadWhiteList(array(
			'href',
			'hreflang',
			'media',
			'rel',
			'target',
		));


		$attrs->add('rel',  $rel)
			  ->add('type', $type)
			  ->add('href', $href);

		if (null !== $content) {
			if (is_array($content)) {
				$this->loadContent($content);
			}
			else {
				$this->addContent($content);
			}
		}
	}
}
