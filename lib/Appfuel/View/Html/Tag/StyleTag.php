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
class StyleTag extends GenericTag
{
	/**
	 * @param	string	$data	content for the title
	 * @return	Title
	 */
	public function __construct($content = null, $type = null)
	{
		if (empty($type) || ! is_string($type)) {
			$type = 'text/css';
		}

		parent::__construct('style');
		$attrs = $this->getTagAttributes();
		$attrs->loadWhiteList(array('media','scope','type'))
			  ->add('type', $type);

		if (is_string($content) && ! empty($content)) {
			$this->addContent($content);
		}

		$this->disableRenderWhenEmpty();
	}
}
