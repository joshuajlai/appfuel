<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\App\View;

use Appfuel\Framework\Exception,
	Appfuel\Framework\App\View\TemplateInterface,
	Appfuel\Framework\App\View\CompositeTemplateInterface,
	Appfuel\Data\Dictionary;

/**
 * The composite 
 */
class CompositeTemplate extends Template implements CompositeTemplateInterface
{
	/**
	 * List of templates used by this template
	 * @var array
	 */
	protected $templates = array();

	/**
	 * Map used to build templates into other templates 
	 * @var	array
	 */
	protected $map = array();

	/**
	 * Determines if template has been added
	 *
	 * @param	scalar	$key	template identifier
	 * @return	bool
	 */
    public function templateExists($key)
	{
		return array_key_exists($key, $this->templates) &&
				$this->templates[$key] instanceof TemplateInterface;
	}

	/**
	 * @param	scalar				$key
	 * @param	TemplateInterface	$template
	 * @return	CompositeTemplate
	 */
	public function addTemplate($key, TemplateInterface $template)
	{
		if (! is_scalar($key)) {
			throw new Exception("Invalid key: must be a scalar value");
		}

		$this->templates[$key] = $template;
		return $this;
	}

	/**
	 * @param	scalar	$key
	 * @return	TemplateInterface | false when no template is found
	 */
	public function getTemplate($key)
	{
		if (! $this->templateExists($key)) {
			return false;
		}

		return $this->templates[$key];
	}

	/**
	 * @param	scalar	$key	
	 * @return	CompositeTemplate
	 */	
	public function removeTemplate($key)
	{
		if (! $this->templateExists($key)) {
			return $this;
		}

		unset($this->templates[$key]);
		return $this;
	}
}
