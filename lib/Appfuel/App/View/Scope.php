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

use SplFileInfo,
	Appfuel\Framework\Exception,
	Appfuel\Framework\App\View\ScopeInterface,
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
		if (! empty($data)) { 
			$this->load($data);
		}
    }

	/**
	 * Load a list of key/value pairs into scope
	 * 
	 * @param	array	$data
	 * @return	Scope
	 */
	public function load(array $data)
	{
		foreach ($data as $key => $value) {
			$this->assign($key, $value);
		}

		return $this;
	}

	/**
	 * @param	string	$label 
	 * @param	mixed	$value
	 * @return	Scope
	 */
	public function assign($label, $value)
	{
		if (! is_scalar($label)) {
			return $this;
		}

		$this->data[$label] = $value;
		return $this;
	}

    /**
     * Get the value for the given label from scope. If the value does not 
	 * exist then return the default parameter
     *
     * @param   string  $label      data label 
     * @param   mixed   $default    value returned used when data not found
     * @return  mixed
     */
    public function get($key, $default = null)
    {   
        if (! $this->exists($key)) {
            return $default;
        }

        return $this->data[$key];
    }
	
	/**
	 * Return all the data in scope
	 * 
	 * @return array
	 */
	public function getAll()
	{
		return $this->data;
	}

    /**
     * echo the value found at label or default if nothing is found
     * 
     * @param   string  $key		label used to identify value in scope
     * @param   mixed   $default	what to render when the key is not found
	 * @param	mixed	$sep		separated used to render an array 
     * @return	null
     */
    public function render($key, $default = '', $sep = ' ')
    {
         if (! $this->exists($key)) {
			if (is_array($default)) {
				$default = implode($sep, $default);;
			} 
			else if (is_object($default) && 
					! method_exists($default, '__toString')) {
				$default = '';
			}

			echo $default;
            return;
        }

        $data = $this->get($key);
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
	 * @return	null
     */
    public function renderAsJson($key, $default = null)
    {
        if (! $this->exists($key)) {
            echo $default;
            return;
        }

        echo json_encode($this->get($key));
    }

    /**
     * @param   string  $label
     * @return  bool
     */
    public function exists($key)
    {
		if (! is_scalar($key)) {
			return false;
		}

        return array_key_exists($key, $this->data);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /** 
     * returns the contents of the file specified as a string. This is used 
     * in conjuction with output buffering to produce a view template
     *
     * @param   File  $file path to template
	 * @return	string
     */
    public function build($file)
    {
		if (is_string($file) && ! empty($file) && file_exists($file)) {
			return $this->includeTemplate($file);
		} 
		else if ($file instanceof SplFileInfo && $file->isFile()) {
			return $this->includeTemplate($file->getRealPath());
		}

		$errTxt = "Could not build template file: file not found ";
		throw new Exception($errTxt);	
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
