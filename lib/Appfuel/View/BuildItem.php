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
namespace Appfuel\View;

use InvalidArgumentException;

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
	 * Flag used to determine if an exeception should be thrown if the 
	 * template can not be found during build
	 * @var bool
	 */
	protected $isSilentFail = true;
	
	/**
	 * @param	mixed	$file 
	 * @param	array	$data
	 * @return	FileTemplate
	 */
	public function __construct($source, $target, $assignLabel)
	{
		$err = 'BuildItem constructor failed: ';
		if (! $this->isValid($source)) {
				$err .= 'source must be non empty scalar';
			throw new InvalidArgumentException($err);
		}
		$this->source = $source;

		if (! $this->isValid($target)) {
			$err .= 'target must be non empty scalar';
			throw new InvalidArgumentException($err);
		}
		$this->target = $target;

		if (! $this->isValid($assignLabel)) {
			$err .= 'assignLabel must be non empty scalar';
			throw new InvalidArgumentException($err);
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
	 * Determines if a result filter is present
	 *
	 * @return	bool
	 */
	public function isResultFilter()
	{
		$filter = $this->resultFilter;
		return is_string($filter) && ! empty($filter) ||
			   is_array($filter) && ! empty($filter) ||
			   is_callable($filter);
	}

	/**
	 * @return bool
	 */
	public function isSilentFail()
	{
		return $this->isSilentFail;
	}

	/**
	 * @return	BuildItem
	 */
	public function enableSilentFail()
	{
		$this->isSilentFail = true;
		return $this;
	}

	/**
	 * @return	BuildItem
	 */
	public function disableSilentFail()
	{
		$this->isSilentFail = false;
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
