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
namespace Appfuel\Console;

use Appfuel\Framework\Exception,
	Appfuel\Framework\File\PathFinder,
	Appfuel\View\Formatter\TextFormatter,
	Appfuel\Framework\Console\ConsoleHelpTemplateInterface;

/**
 * Template used to generate generic html documents
 */
class ConsoleHelpTemplate 
	extends ConsoleViewTemplate implements ConsoleHelpTemplateInterface
{
	/**
	 * Return code for the console
	 * @var int
	 */
	protected $statusCode = null;

	/**
	 * Return error message for the error
	 * @var string
	 */
	protected $statusText = null;
	
	/**
	 * Flag used to determine if we should display the error title line
	 * @var	bool
	 */
	protected $isErrorTitle = true;

	/**
	 * @param	string	$templatePath	path to help template file (optional)
	 * @param	PathFinderInterface $finder	define the relative path (optional)
	 * @param	array	$data	load data into the template (optional)
	 * @param	ViewFormatterInterface $formatter formats the view (optional)
	 * @return	ConsoleHelpTemplate
	 */
    public function __construct($templatePath = null,
                                PathFinderInterface $finder = null,
                                array $data = null,
								ViewFormatterInterface $formatter = null)
    {
		$this->setStatusCode(1);
		$this->setStatusText('unkown error has occured');
		$this->enableErrorTitle();
		parent::__construct($templatePath, $finder, $data, $formatter);
		
    }

    /**
     * @return  scalar
     */
    public function getStatusCode()
    {  
        return $this->statusCode;
    }

    /**
     * @param   scalar  $code
     * @return  JsonTemplate
     */
    public function setStatusCode($code)
    {  
        if (! is_numeric($code)) {
            throw new Exception("console status code must be a numberic value");
        }
        $this->statusCode = $code;
        return $this;
    }

    /**
     * @return  string
     */
    public function getStatusText()
    {  
        return $this->statusText;
    }

    /**
     * @param   string
     * @return  JsonTemplate
     */
    public function setStatusText($text)
    {  
        if (! is_string($text)) {
            throw new Exception("status text must be text");
        }

        $this->statusText = $text;
        return $this;
    }

	/**
	 * @param	int	$code	
	 * @text	string	$text	error title string
	 * @return	ConsoleHelpTemplate
	 */
	public function setStatus($code, $text)
	{
		$this->setStatusText($code)
			 ->setSatusText($text);

		return $this;
	}

	/**	
	 * @return	ConsoleHelpTemplate
	 */
	public function enableErrorTitle()
	{
		$this->isErrorTitle = true;
		return $this;
	}

	/**
	 * @return	ConsoleHelpTemplate
	 */
	public function disableErrorTitle()
	{
		$this->isErrorTitle = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isErrorTitleEnabled()
	{
		return $this->isErrorTitle;
	}

	/**
     * @param   string  $key    template file identifier
     * @param   array   $data   used for private scope
     * @return  string
     */
    public function build(array $data = null, $isPrivate = false)
    {
        $code = $this->getStatusCode();
		$text = $this->getStatusText();
		
        /* we manually assign the new structure.
         */
        $body = parent::build($data, $isPrivate);
		$title = '';
		if ($this->isErrorTitleEnable()) {
			$title = "[$code] $text" . PHP_EOL;
		}
		
		return "{$title}{$body}";
    }
}
