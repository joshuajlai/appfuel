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

use Appfuel\Framework\Exception,
	Appfuel\Framework\App\View\BuildItemInterface;

/**
 * Encapsulates the data needed to build one template into another
 */
class BuildItem implements BuildItemInterface
{
	/**
	 * Key (name) of the source template that we will build
	 * @var string
	 */
	protected $source = null;

	/**
	 * Key (name) of the template the source template will build into
	 * @var	string
	 */
	protected $target = null;

	/**
	 * Label used to assign the result of the source build string 
	 * @var string
	 */
	protected $assignLabel = null;

	/**
	 * callback function used to filter build results
	 * @var	mixed	string | array
	 */
	protected $resultFilter = null;
	
	/**
	 * @param	mixed	$file 
	 * @param	array	$data
	 * @return	FileTemplate
	 */
	public function __construct($source, $target, $assignLabel)
	{
		$err = 'BuildItem constructor failed:';
		if (! $this->isValid($source)) {
			throw new Exception("$err source must be non empty scalar");
		}
		$this->source = $source;

		if (! $this->isValid($target)) {
			throw new Exception("$err target must be non empty scalar");
		}
		$this->target = $target;

		if (! $this->isValid($assignLabel)) {
			throw new Exception("$err assignLabel must be non empty scalar");
		}
		$this->assignLabel = $assignLabel;
	}

	/**
	 * @return	string
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * @return string
	 */
    public function getTarget()
	{
		return $this->target;
	}

	/**
	 * @return string
	 */
	public function getAssignLabel()
	{
		return $this->assignLabel;
	}

	/**
	 * @return string
	 */
	public function getResultFilter()
	{
		return $this->resultFilter;
	}

	/**
	 * @param	mixed	string | array	$function
	 * @return	BuildItem
	 */
	public function setResultFilter($function)
	{
		$this->resultFilter = $function;
		return $this;
	}

	/**
	 * @param	mixed	$str
	 * @return	bool
	 */
	protected function isValid($str)
	{
		return is_scalar($str) && ! empty($str);
	}
}
