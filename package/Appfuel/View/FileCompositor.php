<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\View;

use RunTimeException,
	InvalidArgumentException;

/**
 * The FileCompositor's main responsibility is to compose a template file 
 * (generally .phtml files) into a string. This class is the $this in the 
 * template file and provides the interface to retrieve and render assignments
 * made by the action controller.
 */
class FileCompositor implements FileCompositorInterface
{
    /**
     * Hold name => value pairs to be used in templates
     * @var array
     */
    private $data = array();

	/**
	 * @param	array	$data
	 * @return	FileCompositor
	 */
	public function setData(array $data)
	{
		$this->clear();
		$this->load($data);
		return $this;
	}

    /**
     * Load a list of key/value pairs into template file
     * 
     * @param   array   $data
     * @return  FileCompositor
     */
    public function load(array $data)
    {
        foreach ($data as $key => $value) {
            $this->assign($key, $value);
        }

        return $this;
    }

	/**
	 * @return	FileCompositor
	 */
	public function clear()
	{
		$this->data = array();
	}

	/**
	 * @param	scalar	$key
	 * @param	mixed	$value
	 * @return	FileCompositor
	 */
	public function assign($key, $value)
	{
		if (! is_scalar($key)) {
			throw new InvalidArgumentException(
				"assign failed: key must be a scalar value");
		}

		$this->data[$key] = $value;
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
        $data = $this->get($key, $default);
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
    public function renderAsJson($key, $default = null)
    {
        echo json_encode($this->get($key, $default));
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
    public function exists($key)
    {
		$result = false;
		if (is_scalar($key) && array_key_exists($key, $this->data)) {
			$result = true;
		}

        return $result;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /** 
     * @param   array  $params	
	 * @return	string
     */
    public function compose($file, array $params = null)
    {
		if (! is_string($file) || empty($file)) {
			$err = 'template file path must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		
		if (null !== $params) {
			$this->setData($params);
		}

		return $this->includeTemplate($file);
    }

	/**
	 * @param	string	$file
	 * @param	array	$data
	 * @param	bool	$isEcho
	 * @return	string
	 */
	public function import($file, array $data = null, $isRender = false)
	{
		if (null === $data) {
			$data = array();
		}

		$result = $this->createFileTemplate($file)
					   ->load($data)
					   ->build();

		if (true === $isRender) {
			echo $result;
		}
			
		return $result;
	}

	/**
	 * @param	string	$name
	 * @param	array	$data
	 * @param	string	$vendor
	 * @param	bool	$isRender
	 * @return	string
	 */
	public function importPkg($name, 
							  array $data = null,
							  $vendor     = null, 
							  $isRender   = false)
	{
			
		$result = $this->createFileTemplate($file, true, $vendor)
					   ->load($data)
					   ->build();

		if (true === $isRender) {
			echo $result;
		}
			
		return $result;	
	}

	/**
	 * @param	string	$path
	 * @param	bool	$isPkg
	 * @param	string	$default
	 * @return	FileTemplate
	 */
	private function createFileTemplate($file, $isPkg = false, $default = null)
	{
		return new FileTemplate($file, false);
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
