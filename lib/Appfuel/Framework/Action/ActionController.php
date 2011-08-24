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
namespace Appfuel\Framework\Action;


use Appfuel\Framework\Exception,
    Appfuel\Framework\App\ContextInterface,
    Appfuel\Framework\View\DocumentInterface,
	Appfuel\Framework\View\ViewManagerInterface;

/**
 *
 */
class ActionController implements ControllerInterface
{
	/**
	 * Input scheme handles validation and sanitization of all user inputs
	 * for the controller
	 * @var InputScheme
	 */
	protected $inputScheme = null;

	/**
	 * View Manager handles assignments to the document this controller is
	 * working on.
	 * @var string
	 */
	protected $viewManager = null;

	/**
	 * List of supported documents a controller can act upon
	 * @var array
	 */
	private $supportedDocs = array();

	/**
	 * @return ViewManager
	 */
	public function getViewManager()
	{
		return $this->viewManager;
	}

	/**
	 * @param	ViewManager	$manager
	 * @return	Controller  
	 */
	public function setViewManager(ViewManagerInterface $manager)
	{
		$this->viewManager = $manager;
		return $this;
	}

	/**
	 * @param	array	$types
	 * @return	Controller
	 */
	public function addSupportedDocs(array $types)
	{
		foreach ($types as $type) {
			$this->addSupportedDoc($type);
		}

		return $this;
	}

	/**
	 * @param	string	$type	
	 * @return	Controller
	 */
	public function addSupportedDoc($type)
	{
		if (! is_string($type) || empty($type)) {
			return $this;
		}

		if (in_array($type, $this->supportedDocs)) {
			return $this;
		}

		$this->supportedDocs[] = $type;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSupportedDocs()
	{
		return $this->supportedDocs;
	}

	/**
	 * @param	string $responseType	type of doc used in response
	 * @return	bool
	 */
	public function isSupportedDoc($responseType)
	{
		if (in_array($responseType, $this->supportedDocs)) {
			return true;
		}

		return false;
	}

	/**
	 * 
	 * @param	MessageInterface $msg
	 */
	public function initialize(ContextInterface $msg)
	{
		return $msg;		
	}

	/**
	 * @param	MessageInterface $msg
	 */
	public function execute(ContextInterface $msg)
	{
		return $msg;	
	}
}
