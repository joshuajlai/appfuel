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
 * The link tag defines the relationship between a document and external 
 * resource. Mostly used to link stylesheet
 */
class LinkTag extends GenericTag
{
	/**
	 * @param	string	$href	url or file path to resource
	 * @param	string	$rel	relationship between current doc and link
	 * @param	string  $type	mime type
	 * @return	LinkTag
	 */
	public function __construct($href, $rel = null, $type = null)
	{
		if (empty($href) || ! is_string($href)) {
			$err = 'href must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		parent::__construct('link');
		$this->disableClosingTag();

		$attrs = $this->getTagAttributes();
		$attrs->loadWhiteList(array(
			'href',
			'hreflang',
			'media',
			'rel',
			'sizes',
			'type'
		));

		if (empty($rel) || ! is_string($rel)) {
			$rel = 'stylesheet';
		}

		if (empty($type) || ! is_string($type)) {
			$type = 'text/css';
		}

		$attrs->add('rel',  $rel)
			  ->add('type', $type)
			  ->add('href', $href);
	}
}
