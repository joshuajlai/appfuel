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
class FileCompositor extends BaseCompositor implements FileCompositorInterface
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
	 * Used to resolve relative file paths to absolute paths so the
	 * formatter does not need to care
	 * @var PathFinder
	 */
	private $pathFinder = null;

    /**
	 * The path finder is used to encapsulate the absolute path of the template
	 * file so the view need only set the path as a relative path.
	 *
     * @param   array   $data
     * @return  Template
     */
    public function __construct(PathFinderInterface $pathFinder = null)
    {
		if (null === $pathFinder) {
			$pathFinder = new PathFinder();
		}

		$this->setPathFinder($pathFinder);
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
     * Used with file templates to change the part of the absolute path 
     * from the root to the relative. When isBase is true the root path
     * starts at the end of AF_BASE_PATH.
     *
     * @throws  InvalidArgumentException    when path is not a string
     * @param   string  $path
	 * @param	bool	$isBase				disable the base path 
     * @return  FileCompositor
     */
    public function setRelativeRootPath($path, $isBase = true)
    {  
        $pathFinder = $this->getPathFinder();

        if (false === $isBase && true === $pathFinder->isBasePathEnabled()) {
            $pathFinder->disableBasePath();
        }

        $pathFinder->setRelativeRootPath($path);
        return $this;
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
	 * @return	PathFinderInterface
	 */
	public function getPathFinder()
	{
		return $this->pathFinder;
	}

	/**
	 * @param	PathFinderInterface $finder
	 * @return	FileFormatter
	 */
	public function setPathFinder(PathFinderInterface $finder)
	{
		$this->pathFinder = $finder;
		return $this;
	}

    /** 
     * returns the contents of the file specified as a string. This is used 
     * in conjuction with output buffering to produce a view template
     *
     * @param   File  $file path to template
	 * @return	string
     */
    public function compose(array $data)
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

		$file = $this->getPathFinder()
					 ->getPath($file);

		if (! file_exists($file)) {
			$err  = 'template file does not exist or we do not have correct ';
			$err .= "permissions -($file)";
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
		$formatter = new self($this->getPathFinder());
		$formatter->load($data)
				  ->setFile($file);

		return $formatter->compose($data);
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
