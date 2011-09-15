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

use Appfuel\Framework\View\Formatter\ViewFormatterInterface,
	Appfuel\Framework\View\ViewFileTemplateInterface,
	Appfuel\Framework\File\PathFinderInterface,
	Appfuel\Framework\Exception,
	Appfuel\View\Formatter\TemplateFormatter,
	Countable;

/**
 * A FileView
 */
class ViewFileTemplate extends ViewTemplate implements ViewFileTemplateInterface
{
	/**
	 * Locates relative template paths into absolute paths so view template
	 * is not couple to a particular template location.
	 * @var	PathFinder
	 */
	protected $pathFinder = null;

	/**
	 * @param	mixed	$file 
	 * @param	array	$data
	 * @return	FileTemplate
	 */
	public function __construct($filePath, 
								array $data = null,
								PathFinderInterface $finder = null)
	{
		if (null === $finder) {
			$finder = new ViewPathFinder();
		}
		$this->setPathFinder($finder);

		$filePath = $finder->getPath($filePath);
		if (! file_exists($filePath)) {
			throw new Exception("could not find template at -($filePath)");
		}

		$formatter = new Formatter\TemplateFormatter($filePath);
		parent::__construct($data, $formatter);
	}

	/**
	 * @return	ViewFormatterInterface
	 */
	public function getPathFinder()
	{
		return $this->pathFinder;
	}

	/**
	 * @param	ViewFormatterInterface $formatter
	 * @return	ViewTemplate
	 */
	public function setPathFinder(PathFinderInterface $finder)
	{
		$this->pathFinder = $finder;
		return $this;
	}
}
