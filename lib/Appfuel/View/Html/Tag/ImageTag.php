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
class ImageTag extends GenericTag
{
	/**
	 * @param	string	$href	url or file path to resource
	 * @param	string	$rel	relationship between current doc and link
	 * @param	string  $type	mime type
	 * @return	LinkTag
	 */
	public function __construct($src, $width=null, $height=null, $alt=null)
	{
		if (empty($href) || ! is_string($href)) {
			$err = 'href must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		parent::__construct('img');

		$attrs = $this->getTagAttributes();
		$attrs->loadWhiteList(array(
			'alt',
			'src',
			'usemap',
			'ismap',
			'height',
			'width'
		));


		$this->setSource($src);

		if (null !== $width) {
			$this->setWidth($width);
		}

		if (null !== $height) {
			$this->setHeight($height);
		}

		if (null !== $alt) {
			$this->setAlt($alt);
		}
	}
}
