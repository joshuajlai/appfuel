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
namespace Appfuel\View\Formatter;

use RunTimeException,
	InvalidArgumentException;

/**
 * The template formatter binds a template file with the formatter object. This
 * means the $this in the template file is this object. The format function
 * will convert the template is 
 */
class FileFormatter extends BaseFormatter implements FileFormatterInterface
{
    /**
     * Hold name => value pairs to be used in templates
     * @var array
     */
    private $data = array();

	/**
	 * Path to the template we will bind to 
	 * @var string
	 */
	private $file = null;

    /**
     * @param   array   $data
     * @return  Template
     */
    public function __construct($file = null, array $data = null)
    {
		if (null !== $data) {
			$this->load($data);
		}

		if (null !== $file) {
			$this->setFile($file);
		}
    }

	/**
	 * @return	string
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param	string
	 * @return	CompositeFile
	 */
	public function setFile($file)
	{
		if (empty($file) || ! is_string($file)) {
			$err = 'template file must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		$this->file = $file;
	}

    /**
     * Load a list of key/value pairs into template file
     * 
     * @param   array   $data
     * @return  TemplateFormatter
     */
    public function load(array $data)
    {
        foreach ($data as $key => $value) {
            $this->assign($key, $value);
        }

        return $this;
    }

	/**
	 * @param	scalar	$key
	 * @param	mixed	$value
	 * @return	TemplateFormatter
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
         if (! $this->exists($key)) {
			if (is_array($default)) {
				$default = implode($sep, $default);
			} 
			else if (is_object($default) && 
					 ! is_callable(array($default, '__toString'))) {
				$default = '';
			}

			echo $default;
            return;
        }

        $data = $this->get($key);
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
        if (! $this->exists($key)) {
            echo json_encode($default);
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
    public function format(array $data)
    {
		if (! $this->isValidFormat($data)) {
			$err = 'File formatting failed: data must be an associative array';
			throw new InvalidArgumentException($err);
		}
		$this->load($data);

		$file = $this->getFile();
		if (empty($file) || ! is_string($file)) {
			$err = 'can not format a template when the file path is not set';
			throw new RunTimeException($err);
		}

		if (! file_exists($file)) {
			$err  = 'template file does not exist or we do not have correct ';
			$err .= 'permissions';
			throw new RunTimeException($err);
		}
		
		return $this->includeTemplate($file);
    }

	/**
	 * @param	string $file
	 * @param	array	$data
	 * @return	string
	 */
	public function importTemplate($file, array $data = null)
	{
		if (null === $data) {
			$data = array();
		}
		$formatter = new self($file);
		return $formatter->format($data);
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
