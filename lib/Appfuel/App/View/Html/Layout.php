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
namespace Appfuel\App\View\Html;

use Appfuel\Framework\Exception,
	Appfuel\Framework\View\ViewInterface,
	Appfuel\App\View\FileTemplate;

/**
 * The html layout holds many templates and manages the overal scope for 
 * the variables being used in its variours templates
 */
class Layout extends FileTemplate
{
	/**
	 * Templates used in building the grid
	 * @var DocumentInterface
	 */
	protected $templates = array();

	/**
	 * Datastructure used to hold the order for which templates are build
	 * @var array
	 */
	protected $templateOrder = array();
	
	/**
	 * Add the template file used to render the markup and assign 
	 * any name/value pairs into scope
	 *
	 * @param	array	scope data
	 * @return	Document
	 */
	public function __construct(array $data = array())
	{
		parent::__construct($data);
		$this->addFile('markup', 'grid/grid.phtml');
	}

	/**
	 * Assign a name value pair to a template if it exists. It ignores the
	 * the request when the template can not be found.
	 * 
	 * @param	string	$template	name of the template
	 * @param	string	$label		
	 * @param	mixed	$value
	 * @return	Layout
	 */
	public function assignTo($template, $name, $value)
	{
		if (! $this->templateExists($template)) {
			return $this;
		}

		$this->getTemplate($template)
			 ->assign($name, $value);

		return $this;
	}

	/**
	 * Multple assignments to a template if it exists. Ignored otherwise.
	 *
	 * @param	string	$template
	 * @param	array	$assignments	list of name/value pairs
	 * @return	Layout
	 */
	public function loadTo($template, array $assignments)
	{
		if (! $this->templateExists($template)) {
			return $this;
		}

		$this->getTemplate($template)
			 ->load($assignments);

		return $this;
	}
	
	/**
	 * Returns the order for the template specified. When no template is
	 * given it returns the full order. When the template can not be found
	 * it returns false.
	 *
	 * @param	string	$name name of the template
	 * @return	int | false
	 */
	public function getTemplateOrder($name = null)
	{
		if (null === $name) {
			return $this->templateOrder;
		}

		if (! $this->templateExists($name)) {
			return false;
		}

		return array_search($name, $this->templateOrder, true);
	}

	/**
	 * @param	ViewInterface	$view
	 * @param	string			$name
	 * @return	Grid
	 */
	public function addTemplate($name, ViewInterface $view)
	{
		if (! is_string($name) || empty($name) || $view instanceof Document) {
			return $this;
		}

		$order = count($this->templates) + 1;
		
		/* store the order separately for faster look up of the templates */	
		$this->templateOrder[$order] = $name;
		$this->templates[$name] = array(
			'data'		=> array(),
			'template'	=> $view
		);
		return $this;
	}

	/**
	 * @param	string	$name 
	 * @return	bool
	 */
	public function templateExists($name)
	{
		return  array_key_exists($name, $this->templates) &&
				is_array($this->templates[$name]) && 
				array_key_exists('template', $this->templates[$name]) &&
				$this->templates[$name]['template'] instanceof ViewInterface;
	}

	/**
	 * @param	string	$name
	 * @return	ViewInterface
	 */
	public function getTemplate($name)
	{
		if (! $this->templateExists($name)) {
			return null;
		}	

		return $this->templates[$name]['template'];
	}

	/**
	 * Build the template file into a string
	 * 
	 * @return string
	 */
	public function build($data = null)
	{
		$order = $this->getTemplateOrder();
		
		$content = '';
		foreach ($order as $index => $templateName) {
			$template = $this->getTemplate($templateName);
			$content .= $template->build() . PHP_EOL;
		}

		return $content;
	}
}
