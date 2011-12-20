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
class Link extends Tag
{
	/**
	 * @param	string	$data	content for the title
	 * @return	Title
	 */
	public function __construct($href = null, $rel = null, $type = null)
	{
		$valid = array(
			'href',
			'hreflang',
			'media',
			'rel',
			'sizes',
			'type'
		);
		$this->setTagName('link')
			 ->disableClosingTag()
			 ->addValidAttributes($valid);

		if (null === $rel) {
			$rel = 'stylesheet';
		}

		if (null === $type) {
			$type = 'text/css';
		}
		$this->addAttribute('rel',  $rel)
			 ->addAttribute('type', $type);

		if ($this->isValidString($href)) {
			$this->addAttribute('href', $href);
		}
	}

	/**
	 * Determines if the href is present 
	 * 
	 * @return bool
	 */
	public function isValidHref()
	{
		return $this->attributeExists('href') && ! empty($this->attrs['href']);
	}

	/**
	 * Check the href attribute before building
	 *
	 * @return string
	 */
	public function build()
	{
		if (! $this->isValidHref()) {
			return '';
		}

		return parent::build();
	}
}
