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

use RunTimeException;

/**
 * Allows authors to add interativity to the html document. In this 
 * implementation the tag will only build under the following conditions:
 *
 * 1) src attribute is present and there is no content
 * 2) content is available and there is no src attribute
 */
class ScriptTag extends GenericTag
{
	/**
	 * @param	string	$data	content for the title
	 * @return	Title
	 */
	public function __construct($src = null, $content = null, $type = null)
	{
		if (null !== $src && null !== $content) {
			$err  = 'It is a runtime error to set both script source and ';
			$err .= 'content';
			throw new RunTimeException($err);
		}

		if (null === $type || ! is_string($type)) {
			$type = 'text/javascript';
		}

		parent::__construct('script');
		$attrs = $this->getTagAttributes();
		$attrs->loadWhiteList(array('async','charset','defer','src','type'))
			  ->add('type', 'text/javascript');

		if (null !== $content) {
			$this->addContent($content);
		}

		if (null !== $src && is_string($src) && ($src = trim($src))) {
			$attrs->add('src', $src);
		}

		$this->disableRenderWhenEmpty();
	}

	/**
	 * @throws	RunTimeException	when content as already been added
	 * @param	string	$name	
	 * @param	string	$value	default null
	 * @return	ScriptTag
	 */
	public function addAttribute($name, $value = null)
	{
		if ('src' === $name && ! $this->isEmpty()) {
			$err = 'can not add a source attribute to a script with content';
			throw new RunTimeException($err);
		}

		return parent::addAttribute($name, $value);
	}

	/**
	 * @throws	RunTimeException	when src is already added
	 * @param	string	$name	
	 * @param	string	$action		default append
	 * @return	ScriptTag
	 */
	public function addContent($name, $action = 'append')
	{
		if ($this->isAttribute('src')) {
			$err = 'can not add content to a script with a src attribute';
			throw new RunTimeException($err);
		}

		return parent::addContent($name, $action);
	}

	/**
	 * @return string
	 */
	public function build()
	{
		$attrs   = $this->getTagAttributes();
		$content = $this->getTagContent();
	
		if ($attrs->exists('src')) {
			return $this->buildTag('', $attrs); 
		}

		if (false === $this->isRenderWhenEmpty() && $content->isEmpty()) {
			return '';
		}

		return $this->buildTag($content, $attrs);
	}
}
