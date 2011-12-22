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

use RunTimeException;

/**
 * Allows authors to add interativity to the html document. In this 
 * implementation the tag will only build under the following conditions:
 *
 * 1) src attribute is present and there is no content
 * 2) content is available and there is no src attribute
 *
 * In each case the type attribute is set with text/javascript mime
 */
class Script extends Tag
{
	/**
	 * @param	string	$data	content for the title
	 * @return	Title
	 */
	public function __construct($src = null, $content = null)
	{
		$valid = array(
			'async',
			'charset',
			'defer',
			'src',
			'type'
		);
		$this->setTagName('script')
			 ->addValidAttributes($valid);

		$this->addAttribute('type', 'text/javascript');

		if (null !== $src && null !== $content) {
			$err  = 'It is a runtime error to set both script source and ';
			$err .= 'content';
			throw new RunTimeException($content);
		}

		if (null !== $content && $this->isValidString($content)) {
			$this->addContent($content);
		}

		if (null !== $src && is_string($src) && ($src = trim($src))) {
			$this->addAttribute('src', $src);
		}
	}

	/**
	 * @return string
	 */
	public function build()
	{
		$content = '';
		$count   = $this->contentCount();
		if ($this->attributeExists('src') && 0 === $count) {
			$content = parent::build();
		} 
		else if (! $this->attributeExists('src') && $count > 0) {
			$content = parent::build();
		}
		else {
			$content = '';
		}

		return $content;
	}
}
