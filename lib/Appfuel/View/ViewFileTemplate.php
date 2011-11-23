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

use RunTimeException,
	InvalidArgumentException,
	Appfuel\View\Formatter\ViewFormatterInterface,
	Appfuel\Kernel\PathFinder,
	Appfuel\Kernel\PathFinderInterface,
	Appfuel\View\Formatter\TemplateFormatter,
	Countable;

/**
 * A ViewFileTemplate is a view template that is designed to work with the
 * template formatter. It has a path finder to locate template files. 
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
	 * @param	mixed	$filePath
	 * @param	array	$data
	 * @param	mixed null|string|PathFinderInterface $finder
	 * @return	ViewFileTemplate
	 */
	public function __construct($filePath, 
								array $data = null,
								$finder = null)
	{
		if (empty($filePath) || ! is_string($filePath)) {
			throw new Exception("File path must be a non empty string");
		}

		if (null === $finder) {
			$finder = new PathFinder('ui/appfuel');
		}
		else if (is_string($finder) && ! empty($finder)) {
			$finder = new PathFinder($finder);
		}
		else if (! ($finder instanceof PathFinderInterface)) {
			$err  = 'Finder must be null, a string or implement ';
			$err .= 'Appfuel\Kernel\PathFinderInterface';
			throw new InvalidArgumentException($err);
		}
		$this->setPathFinder($finder);

		$filePath = $finder->getPath($filePath);
		if (! file_exists($filePath)) {
			$err = "could not find template at -($filePath)";
			throw new RunTimeException($err);
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
