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
namespace Appfuel\Kernel\Mvc;

use RunTimeException;

/**
 * Custom kernel exception to indicate that a route has not been mapped
 */
class MvcException extends RunTimeException
{
	/**
	 * @var ViewTemplateInterface
	 */
	protected $view = null;

    /**
     * @param   string  $requestString
     * @return  Uri
     */
    public function __construct($msg, $code, ViewTemplateInterface $view)
	{
		parent::__construct($msg, $code);
		$this->view = $view;
	}

	/**
	 * @return	ViewTemplateInterface
	 */
	public function getView()
	{
		return $this->view;
	}
}
