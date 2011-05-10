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
namespace Appfuel\View\Html\Element\Meta;

use Appfuel\View\Html\Element\Tag as ParentTag;

/**
 * The base tag specifies a default url and/or a default target for all 
 * elements with a url(hyperlinks, images, forms, etc..). This tag must
 * live in the head element but thats not enforced. 
 */
class Tag extends ParentTag
{
	/**
	 * Only has two valid attributes href and target
	 *
	 * @return	base
	 */
	public function __construct($name = null, $content = null)
	{
		$validAttrs = array(
			'charset',
			'content',
			'http-equiv',
			'name'
		);
		$this->setTagName('meta')
			 ->disableClosingTag()
			 ->addValidAttributes($validAttrs);
			

		if ($this->isValidString($name) && $this->isValidString($content)) {
			$this->addAttribute('name', $name)
				 ->addAttribute('content', $content);
		}
	}

	/**
	 * 
	 * @param	string	$name
	 * @param	string	$value
	 * @return	Tag
	 */
	public function addAttribute($key, $value)
	{
		switch ($key) {
			case 'name':
				if (! $this->attributeExists('charset') && 
					! $this->attributeExists('http-equiv')) {
					parent::addAttribute('name', $value);
				}
				break;
			case 'charset':
				if (! $this->attributeExists('name') && 
					! $this->attributeExists('http-equiv')) {
					parent::addAttribute('charset', $value);
				}
				break;
			case 'http-equiv':
				if (! $this->attributeExists('charset') && 
					! $this->attributeExists('name')) {
					parent::addAttribute('http-equiv', $value);
				}
				break;

			default:
				parent::addAttribute($key, $value);
		}

		return $this;
	}

	/**
	 * Determines if a attributes are valid for a charset
	 * 
	 * @return bool
	 */
	public function isValidCharset()
	{
		return $this->attributeExists('charset') &&
			   ! $this->attributeExists('name')  &&
			   ! $this->attributeExists('http-equiv') &&
			   ! $this->attributeExists('content');
	}

	/**
	 * Determines if a attributes are valid for a http-equiv
	 * 
	 * @return bool
	 */
	public function isValidHttpEquiv()
	{
		return $this->attributeExists('http-equiv') &&
			   $this->attributeExists('content') &&
			   ! $this->attributeExists('name')  &&
			   ! $this->attributeExists('charset');
	}

	/**
	 * Determines if a attributes are valid for a name
	 * 
	 * @return bool
	 */
	public function isValidName()
	{
		return $this->attributeExists('name') &&
			   $this->attributeExists('content') &&
			   ! $this->attributeExists('http-equiv')  &&
			   ! $this->attributeExists('charset');
	}

	/**
	 * Only build valid charset, http-equiv or name meta tags
	 *
	 * @return	string
	 */
	public function build()
	{
		if ($this->isValidCharset()   || 
			$this->isValidHttpEquiv() ||
			$this->isValidName() ) {
			return parent::build();
		}

		return '';
	}
}
