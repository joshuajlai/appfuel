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
namespace Appfuel\View\Compositor;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\PathFinder,
	Appfuel\Kernel\PathFinderInterface;

/**
 * The FileCompositor's main responsibility is to compose a template file 
 * (generally .phtml files) into a string. This class is the $this in the 
 * template file and provides the interface to retrieve and render assignments
 * made by the action controller.
 */
class TemplateCompositor
{
    /**
     * Hold name => value pairs to be used in templates
     * @var array
     */
    private $data = array();


    /**
     * Load a list of key/value pairs into template file
     * 
     * @param   array   $data
     * @return  TemplateFormatter
     */
    public function setData(array $data)
    {
		$this->data = $data;
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
    public function get($key, $default = null, $ns = null)
    {
        if (! is_string($key) || strlen($key) === 0) {
            return $default;
        }

        if (null === $ns) {
            $ns = 'global';
        }

        if (! isset($this->data[$ns]) || ! isset($this->data[$ns][$key])) {
            return $default;
        }

        return $this->data[$ns][$key];
    }

    /**
     * @param   string  $ns
     * @return  array
     */
    public function getAll($ns = null)
    {
        if (null === $ns) {
            $ns = 'global';
        }

        if (! isset($this->data[$ns])) {
            return false;
        }

        return $this->data[$ns];
    }

    /**
     * @return  array
     */
    public function getAllNamespaces()
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
    public function render($key, $default = '', $ns = null, $sep = ' ')
    {
        $data = $this->get($key, $default, $ns);
        if (is_array($data)) {
            $data = implode($sep, $data);
        } elseif (is_object($data) && 
				 ! is_callable(array($data, '__toString'))) {
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
    public function renderAsJson($key, $default = null, $ns = null)
    {
        echo json_encode($this->get($key, $default, $ns));
    }

	/**
	 * @return	null
	 */
	public function renderEOL()
	{
		echo PHP_EOL;
	}

    /**
     * @param   string  $label
     * @return  bool
     */
    public function exists($key, $ns = null)
    {
        if (! is_string($key) || strlen($key) === 0) {
            return false;
        }

        if (null === $ns) {
            $ns = 'global';
        }

		if (! isset($this->data[$ns]) || 
			! array_key_exists($key, $this->data[$ns][$key])) {
			return false;
		}
        return true;
    }

    /**
     * @return int
     */
    public function count($ns = null)
    {
		if (null === $ns) {
			$ns = 'global';
		}
		if (! isset($this->data[$ns])) {
			return false;
		}

        return count($this->data[$ns]);
    }

	/**
	 * @return	int
	 */
	public function countAllNamspaces()
	{
		$total = 0;
		foreach ($this->data as $ns => $data) {
			$total += count($data);
		}

		return $total;
	}

    /** 
     * returns the contents of the file specified as a string. This is used 
     * in conjuction with output buffering to produce a view template
     *
     * @param   File  $file path to template
	 * @return	string
     */
    public function compose($file, array $data)
    {
		$this->setData($data);

		if (empty($file) || ! is_string($file)) {
			$err = 'can not format a template when the file path is not set';
			throw new RunTimeException($err);
		}
		
		$bind = FileViewManager::getTemplateBinder($file);
		
		return $bind();
    }

	/**
	 * @param	string	$file
	 * @param	array	$data
	 * @param	bool	$isEcho
	 * @return	string
	 */
	public function import($file, array $data = null, $isEcho = false)
	{
		if (null === $data) {
			$data = array();
		}
		$formatter = new self($this->getPathFinder());

		$formatter->load($data)
				  ->setFile($file);

		$result = $formatter->compose($data);
		if (true === $isEcho) {
			echo $result, PHP_EOL;
			return;
		}

		return $result;
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
    private function includeTemplate()
    {
        ob_start();
        include func_get_arg(0);
        $contents = ob_get_contents();
        ob_end_clean();
        return trim($contents, " \n");
    }
}
