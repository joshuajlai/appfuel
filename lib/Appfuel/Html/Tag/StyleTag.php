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
namespace Appfuel\Html\Tag;

/**
 * The link tag defines the relationship between a document and external 
 * resource. Mostly used to link stylesheet
 */
class StyleTag extends GenericTag
{
	/**
	 * @param	mixed	$data	style content
	 * @param	string	$type	
	 * @param	string	$sep	content separator 
	 * @return	StyleTag
	 */
	public function __construct($data = null, $type = null, $sep = null)
	{
		$this->disableRenderWhenEmpty();
		if (empty($type) || ! is_string($type)) {
			$type = 'text/css';
		}

		if (null === $sep) {
			$sep = PHP_EOL;
		}
		
		$content = new TagContent($data, $sep);
		$attrs   = new TagAttributes(array('media', 'scope', 'type'));
		$attrs->add('type', $type);
		parent::__construct('style', $content, $attrs);
	}
}
