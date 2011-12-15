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
namespace Appfuel\View;

use Appfuel\View\Compositor\ViewCompositorInterface;

/**
 * The view template is the most basic of the templates. Holding all its data
 * in key/value pair it uses a formatter to convert it a string.
 */
interface ViewTemplateInterface
{
	/**
	 * @return	ViewFormatterInterface
	 */
	public function getViewCompositor();

	/**
	 * @param	ViewFormatterInterface $formatter
	 * @return	ViewTemplate
	 */
	public function setViewCompositor(ViewCompositorInterface $compositor);
    
	/**
     * Determines if template has been added
     *
     * @param   scalar  $key    template identifier
     * @return  bool
     */
    public function isTemplate($key);

    /**
     * @param   string					$key
     * @param   ViewTemplateInterface   $template
     * @return  ViewTemplateInterface
     */
    public function addTemplate($key, ViewTemplateInterface $template);

    /**
     * @param   string  $key
     * @return  ViewTemplateInterface | false when no template is found
     */
    public function getTemplate($key);

    /**
     * @param   string  $key    
     * @return  ViewTemplateInterface
     */
    public function removeTemplate($key);

	/**
	 * This will add an entry that will tell the build to turn the
	 * source template into a string and using the label assign it to 
	 * the destination template. When the label is not given it will you 
	 * the source key as the assignment label
	 *
	 * @param	string	$source			template key for source template
	 * @param	string  $destination	template key for destination
	 * @param	string  $label			assignement label to destination
	 * @return	ViewTemplate
	 */
	public function assignTemplate($source, $destination = null, $label=null);

	/**
	 * @return	array
	 */
	public function getTemplateAssignments();

	/**
	 * @return	int
	 */
	public function templateCount();

	/**
	 * @return	int
	 */
	public function assignCount();

	/**
	 * @param	array	$data
	 * @return	ViewTemplateInterface
	 */
	public function load(array $data);

	/**
	 * Assign key value pair into the template. This assignment will not reach
	 * the templates scope until the build method has been used to convert it
	 * into a string. IsDeep only applies to composite templates not leaves 
	 * which searches templates in templates and assigns the last one the
	 * key value
	 *
	 * @param	string	$key
	 * @param	mixed	$value
	 * @return	ViewTemplateInterface
	 */
	public function assign($key, $value);

	/**
	 * Assign a key=>value pair to one of this templates template. When is
	 * deep is true then the key will be exploded on '.' and the last element
	 * treated as the key all other elements are treated as template key and
	 * traversed through the template graph to the last template.
	 *
	 * @param	string	$key
	 * @param	mixed	$value
	 * @param	bool	$isDeep
	 * @return	ViewTemplate
	 */
	public function assignTo($key, $value);

	/**
	 * Retrieve value assigned into the template. In the case of template
	 * files, assignments are not bound until the formatter binds them
	 * during build.
	 *
	 * @param	string	$key
	 * @return	mixed | default on failure
	 */
	public function get($key, $default = null);

	/**
	 * @return	array
	 */
	public function getAll();

	/**
	 * @param	string
	 * @return	bool
	 */
	public function isAssigned($key);

	/**
	 * Retrieve an assigned value from another template store in this template
	 *
	 * @param	string	$key
	 * @param	mixed	$default
	 * @return	null
	 */
	public function getFrom($key, $default = null);

	/**
	 * Build the template file indicated by key into string. Use data in
	 * the dictionary as scope
	 *
	 * @param	string	$key	template file identifier
	 * @param	array	$data	used for private scope
	 * @return	string
	 */
    public function build();

	/**
	 * Build template into other templates
	 * @return	null
	 */
	public function buildTemplates();

	/**
	 * This should not throw any exceptions. Any errors will result is any
	 * empty string
	 *
	 * @return	string
	 */
	public function __toString();

	/**
	 * Assign the top template to this template then traverse down each
	 * template getting the next template until we found the template at
	 * the bottom of the tree.
	 * 
	 * @param	array	$keys
	 * @return	ViewTemplateInterface | string of the key not found
	 */
	public function traverseTemplates(array $keys);
}
