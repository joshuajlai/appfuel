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
namespace Appfuel\App;

use SplFileInfo,
	Appfuel\Framework\View\ScopeInterface,
	Appfuel\Stdlib\Filesystem\Manager as FileManager;

/**
 * Binds a template file (example.phtml) with the scope of the template object 
 * that can be resolved through the $this contruct and can be rendered into a
 * string
 */
class Scope implements ScopeInterface
{
    /**
     * Hold name => value pairs to be used in templates
     * @var array
     */
    private $data = array();

    /**
     * @param   array   $data
     * @return  Template
     */
    public function __construct(array $data = array())
    {   
        $this->load($data);
    }

    /**
     * Pulls data out of this scope. When the label is not found default is used 
     * instead
     *
     * @param   string  $label      data label 
     * @param   mixed   $default    value returned used when data not found
     * @return  mixed
     */
    public function get($label, $default = NULL)
    {   
        if (! $this->exists($label)) {
            return $default;
        }

        return $this->data[$label];
    }

    /**
     * echo the value found at label or default if nothing is found
     * 
     * @param   string  $label
     * @param   mixed   $value
     * @return string
     */
    public function render($label, $default = '', $sep = ' ')
    {
         if (! $this->exists($label)) {
            echo $default;
            return;
        }

        $data = $this->data[$label];
        if (is_array($data)) {
            $data = implode($sep, $data);
        } elseif (is_object($data) && ! method_exists($data, '__toString')) {
            $data = '';
        }

        echo $data;
    }

    /**
     * Render Json
     * Helper function to generate a json encoded string of the contents
     * specified by the key.
     *
     * @param   string  $key
     */
    public function renderAsJson($key, $default = NULL)
    {
        if (! $this->exists($key)) {
            echo $default;
            return;
        }

        $value = $this->get($key, NULL);
        if (is_object($value)) {
            throw new Exception('Invalid value for render json');
        }

        echo json_encode($value);
    }

    /**
     * Adds any label value pair into scope for use by template files
     *
     * @param   string  $label  any string or object that supports __toString  
     * @param   mixed   $value
     * @return  NULL
     */
    public function assign($label, $value)
    {
        if (! is_scalar($label)) {
            throw new Exception(
                "Invalid scope label: must be a string"
            );
        }

        $this->data[$label] = $value;
        return $this;
    }

    /**
     * @param   string  $label
     * @return  bool
     */
    public function exists($label)
    {
        return array_key_exists($label, $this->data);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * load an array of label value pairs. We foreach here
     * because we want to validate that each label is a proper string
     *
     * @param   array   $items
     * @return  void
     */
    public function load(array $items)
    {
        foreach ($items as $label => $value) {
            $this->assign($label, $value);
        }

        return $this;
    }

    /**
     * Merge the data of another scope into this one
     * 
     * @param   Scope   $scope
     * @return  void
     */
    public function merge(ScopeInterface $scope)
    {
        $this->data = array_merge($this->data, $scope->getAll());
        return $this;
    }

    /** 
     * returns the contents of the file specified as a string. This is used 
     * in conjuction with output buffering to produce a view template
     *
     * @param   File  $file path to template
     */
    public function build(SplFileInfo $file)
    {
        if (! $file->isFile()) {
            throw new Exception(
                "Template file $file could not be found or is not readable"
            );
        }

        return $this->includeTemplate($file->getRealPath());
    }

    /**
     * Include Template
     * Uses output buffering to store the content and return it. The 
     * incoming parameter are obtained via func_get_arg to prevent 
     * the variable becoming visible in the template scope
     *
     * @param   string  arguement 0     file path to template
     * @return  string
     */
    protected function includeTemplate()
    {
        ob_start();
        include func_get_arg(0);
        $contents = ob_get_contents();
        ob_end_clean();
        return trim($contents, " \n");
    }
}
