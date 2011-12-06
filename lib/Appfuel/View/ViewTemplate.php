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

use RunTimeException,
	InvalidArgumentException,
	Appfuel\View\Compositor\FileFormatter,
	Appfuel\View\Compositor\TextFormatter,
	Appfuel\View\Formatter\FileFormatterInterface,
	Appfuel\View\Formatter\ViewFormatterInterface;

/**
 * The view template is the most basic of the templates. Holding all its data
 * in key/value pair it uses a formatter to convert it a string.
 */
class ViewTemplate implements ViewTemplateInterface
{
	/**
	 * List of other template used by this template
	 * @var array
	 */
	protected $templates = array();

	/**
	 * Strategy used to be build and assign templates
	 * @var array
	 */
	protected $templateBuild = array();


	/**
	 * Holds assignment until build time where they are passed into scope
	 * @var array
	 */
	protected $assign = array();

	/**
	 * The formatter turns the assignments into a string. The compositor
	 * is only reponsible for its own assignments
	 * @var	ViewFormatterInterface
	 */
	protected $formatter = null;

	/**
	 * Relative path to a file template
	 * @var string
	 */
	protected $file = null;


	/**
	 * @param	mixed	$file 
	 * @param	array	$data
	 * @return	FileTemplate
	 */
	public function __construct(array $data = null)
	{
		if (null !== $data) {
			$this->load($data);
		}
	}

	/**
	 * Relative file path to template file
	 * @return	null
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param	string	$file
	 * @return	ViewTemplate
	 */
	public function setFile($file)
	{
		if (empty($file) || ! is_string($file) || ! ($file = trim($file))) {
			$err = 'file path must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->file = $file;
		return $this;
	}

	/**
	 * When a file is set with a non empty string it indicates that this 
	 * template will be formatted with a FileFormatter.
	 *
	 * @return	bool
	 */
	public function isFileTemplate()
	{
		return ! empty($this->file) && is_string($this->file);
	}

	/**
	 * @return	ViewFormatterInterface
	 */
	public function getViewFormatter()
	{
		return $this->formatter;
	}

	/**
	 * @param	ViewFormatterInterface $formatter
	 * @return	ViewTemplate
	 */
	public function setViewFormatter(ViewFormatterInterface $formatter)
	{
		$this->formatter = $formatter;
		return $this;
	}
    /**
     * Determines if template has been added
     *
     * @param   scalar  $key    template identifier
     * @return  bool
     */
    public function isTemplate($key)
    {
		if (! empty($key) || 
			is_string($key) || 
			isset($this->templates[$key]) ||
			$this->templates[$key] instanceof ViewTemplateInterface) {
			return true;
		}

		return false;
    }

    /**
     * @param   scalar              $key
     * @param   TemplateInterface   $template
     * @return  CompositeTemplate
     */
    public function addTemplate($key, ViewTemplateInterface $template)
    {
        if (empty($key) || ! is_string($key)) {
			$err = 'addTemplate failed: key must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        $this->templates[$key] = $template;
        return $this;
    }

    /**
     * @param   scalar  $key
     * @return  TemplateInterface | false when no template is found
     */
    public function getTemplate($key)
    {
        if (! $this->isTemplate($key)) {
            return false;
        }

        return $this->templates[$key];
    }

    /**
     * @param   scalar  $key    
     * @return  CompositeTemplate
     */
    public function removeTemplate($key)
    {
        if (! $this->isTemplate($key)) {
            return $this;
        }

        unset($this->templates[$key]);
        return $this;
    }

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
	public function assignTemplate($source, $destination = null, $label = null)
	{
		$err = 'failed to assign template: ';
		if (empty($source) || ! is_string($source)) {
			$err .= 'the template key must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (null === $destination) {
			$destination = 'this';
		}

		if (empty($destination) || ! is_string($destination)) {
			$err = 'destination template key must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		/* if no assignment label is given use the source template's key */
		if (null === $label) {
			$label = $source;
		}
	
		if (empty($label) || ! is_string($label)) {
			$err .= 'the assignment label must be a non empty string';
			throw new InvalidArgumentException($err);
		}


		$this->templateBuild[$source] = array($destination, $label);
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getTemplateAssignments()
	{
		return $this->templateBuild;
	}

	/**
	 * @return	int
	 */
	public function templateCount()
	{
		return count($this->template);
	}

	/**
	 * @return	int
	 */
	public function assignCount()
	{
		return count($this->assign);
	}

	/**
	 * @param	array	$data
	 * @return	ViewTemplate
	 */
	public function load(array $data)
	{
		foreach ($data as $key => $value) {
			$this->assign($key, $value);
		}

		return $this;
	}

	/**
	 * Assign key value pair into the template. This assignment will not reach
	 * the templates scope until the build method has been used to convert it
	 * into a string. IsDeep only applies to composite templates not leaves 
	 * which searches templates in templates and assigns the last one the
	 * key value
	 *
	 * @param	scalar	$key
	 * @param	mixed	$value
	 * @return	ViewTemplate
	 */
	public function assign($key, $value)
	{
        if (! is_scalar($key)) {
			throw new InvalidArgumentException(
				"Template assignment keys must be scalar "
			);
        }

        $this->assign[$key] = $value;
		return $this;
	}

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
	public function assignTo($key, $value)
	{
		$err = 'assignTo failed: ';
		if (empty($key) && ! is_string($key)) {
			$err .= 'assignTo failed: key must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (false === strpos($key, '.')) {
			$template = $this->getTemplate($key);
			if (! ($template instanceof ViewTemplateInterface)) {
				$err .= "template not found at -($key)";
				throw new RunTimeException($err);
			}
			$template->assign($key, $value);
			return $this;
		}

		$parts = explode('.', $key);
		if (empty($parts)) {
			$err .= "no templates found for -($key)";
			throw new RunTimeException($err);
		}

		$label = array_pop($parts);
		$template = $this->traverseTemplates($part);
		if (! ($template instanceof ViewTemplateInterface)) {
			$err .= "no template found at -($template)";
			throw new RunTimeException($err);
		}

		$template->assign($label, $value);
		return $this;
	}

	/**
	 * @param	string	$key
	 * @return	mixed | default on failure
	 */
	public function get($key, $default = null)
	{	
		if (! $this->isAssigned($key)) {
			return $default;
		}

		return $this->assign[$key];
	}

	/**
	 * @param	string
	 * @return	bool
	 */
	public function isAssigned($key)
	{
		if (empty($key) || ! is_string($key) || ! isset($this->assign[$key])) {
			return false;
		}

		return true;
	}

	/**
	 * @param	string	$key
	 * @param	mixed	$default
	 * @return	null
	 */
	public function getFrom($key, $default = null)
	{
		if (empty($key) || ! is_string($key)) {
			return $default;
		}

		if (false === strpos($key, '.')) {
			$template = $this->getTemplate($key);
			if (! ($template instanceof ViewTemplateInterface)) {
				return $default;
			}
			return $template->get($key, $default);
		}

		$parts = explode('.', $key);
		if (empty($parts)) {
			$err .= "no templates found for -($key)";
			throw new RunTimeException($err);
		}

		$key = array_pop($parts);
		$template = $this->traverseTemplates($parts);
		if (! ($template instanceof ViewTemplateInterface)) {
			return $default;
		}

		return $template->get($key, $default);
	}

	/**
	 * @return	array
	 */
	public function getAll()
	{
		return $this->assign;
	}

	/**
	 * Build the template file indicated by key into string. Use data in
	 * the dictionary as scope
	 *
	 * @param	string	$key	template file identifier
	 * @param	array	$data	used for private scope
	 * @return	string
	 */
    public function build()
	{
		$formatter = $this->getViewFormatter();
		if (! ($formatter instanceof ViewFormatterInterface)) {
			$err  = 'build failed: can not build without a view formatter or ';
			$err .= 'view formatter that does not implement a Appfuel\View';
			$err .= '\Formatter\ViewFormatterInterface';
			throw new RunTimeException($err);
		}

		if ($this->templateCount() > 0) {
			$this->buildTemplates();
		}

		if ($this->isFileTemplate()) {
			$file = $this->getFile();
			if (! ($formatter instanceof FileFormatterInterface)) {
				$err  = 'build failed: when a template file is set the view ';
				$err .= 'formatter must implement Appfuel\View\FileFormatter';
				$err .= 'Interface';
				throw new RunTimeException($err);
			}
			$formatter->setFile($file);
		}

		return $formatter->format($this->getAll());
	}

	/**
	 * Build template into other templates
	 * @return	null
	 */
	public function buildTemplates()
	{
		$error  = 'template build failed: ';
		$result = '';
		$assignments = $this->getTemplateAssignments();
		foreach ($assignments as $source => $data) {
			if (! isset($data[0]) || ! isset($data[1])) {
				$error .= "malformed template build -($source)";
				throw new RunTimeException($err);
			}
			$target = $data[0];
			$label  = $data[1];
			if (! $this->isTemplate($sourceTemplate)) {
				$error .= 'source template not found';
				throw new RunTimeException($err);
			}
			$sourceTemplate = $this->getTemplate($source);
	
			if (! $this->isTemplate($target)) {
				$error .= 'target template not found';
				throw new RunTimeException($err);
			}

			if ('this' === $target) {
				$this->assign($label, $source->build());
			}
			else {
				$targetTemplate = $this->getTemplate($target);
				$targetTemplate->assign($label, $sourceTemplate->build());
			}
		}
	}

	/**
	 * This should not throw any exceptions. Any errors will result is any
	 * empty string
	 *
	 * @return	string
	 */
	public function __toString()
	{
		try {
			$result = $this->build();
		} catch (Exception $e) {
			$result = '';
		}

		return $result;
	}

	/**
	 * Assign the top template to this template then traverse down each
	 * template getting the next template until we found the template at
	 * the bottom of the tree.
	 * 
	 * @param	array	$keys
	 * @return	ViewTemplateInterface | string of the key not found
	 */
	public function traverseTemplates(array $keys) 
	{
		$template = $this;
		foreach ($keys as $templateKey) {
			$template = $template->getTemplate($templateKey);
			if (! ($template instanceof ViewTemplateInterface)) {
				return $templateKey;
			}
		}

		return $template;
	}
}
