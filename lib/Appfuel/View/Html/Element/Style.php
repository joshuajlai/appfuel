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
namespace Appfuel\View\Html\Element;

use Appfuel\Framework\Exception;

/**
 * The link tag defines the relationship between a document and external 
 * resource. Mostly used to link stylesheet
 */
class Style extends Tag
{
	/**
	 * @param	string	$data	content for the title
	 * @return	Title
	 */
	public function __construct($content = null, $type = null)
	{
		$valid = array(
			'media',
			'scope',
			'type'
		);
		$this->setTagName('style')
			 ->addValidAttributes($valid);

		if (null === $type) {
			$type = 'text/css';
		}	
		$this->addAttribute('type', $type);

		if ($this->isValidString($content)) {
			$this->addContent($content);
		}
	}

	/**
	 * Only render when content is available
	 * 
	 * @return string
	 */
	public function build()
	{
		if (0 === $this->contentCount()) {
			return '';
		}

		return parent::build();
	}
}
