<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use InvalidArgumentException;

/**
 * Controls view settings like disabling the view or telling the framework
 * you will handle the view manually, what the default format for the view is
 * and in the case of html pages what what does this view use.
 */
class RouteView implements RouteViewInterface
{
	/**
	 * Used to determine what view format will be used with this route
	 * @var string
	 */
	protected $format = 'html';

	/**
	 * @var	bool
	 */
	protected $isViewDisabled = false;

	/**
	 * Determines if the framework needs to compose the view from the view data
	 * @var bool
	 */
	protected $isManualView = false;

	/**
	 * Name of the view package which represents the view for this route.
	 * View packages are generally html pages
	 * @var string
	 */
	protected $viewPkg = null;

	/**
	 * @return string
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	public function setFormat($name)
	{
		if (! is_string($name)) {
			$err = 'route format must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->format = $name;
		return $this;
	}

	/**
	 * @return	RouteView
	 */
	public function disableView()
	{
		$this->isViewDisabled = true;
		return $this;
	}

	/**
	 * @return	RouteView
	 */
	public function enableView()
	{
		$this->isViewDisabled = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isViewDisabled()
	{
		return $this->isViewDisabled;
	}

	/**
	 * @return	RouteView
	 */
	public function enableManualView()
	{
		$this->isManualView = true;
		return $this;
	}

	/**
	 * @return	RouteView
	 */
	public function disableManualView()
	{
		$this->isManualView = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isManualView()
	{
		return $this->isManualView;
	}

	/**
	 * @return	bool
	 */
	public function isViewPackage()
	{
		return is_string($this->viewPkg) && ! empty($this->viewPkg);
	}

	/**
	 * @return	string
	 */
	public function getViewPackage()
	{
		return $this->viewPkg;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	public function setViewPackage($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = "package name must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->viewPkg = $name;
		return $this;
	}

	/**
	 * @return	RouteView
	 */
	public function clearViewPackage()
	{
		$this->viewPkg = null;
		return $this;
	}
}
