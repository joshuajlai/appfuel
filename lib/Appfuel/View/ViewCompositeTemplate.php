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

use Appfuel\Framework\Exception,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\File\PathFinderInterface,
	Appfuel\Framework\View\ViewTemplateInterface,
	Appfuel\Framework\View\BuildItemInterface,
	Appfuel\Framework\View\ViewCompositeTemplateInterface,
	Appfuel\Framework\View\Formatter\ViewFormatterInterface;

/**
 * The composite 
 */
class ViewCompositeTemplate 
	extends ViewFileTemplate implements ViewCompositeTemplateInterface
{
	/**
	 * List of templates used by this template
	 * @var array
	 */
	protected $templates = array();

	/**
	 * Map used to build templates into other templates 
	 * @var	array
	 */
	protected $buildItems = array();

	/**
	 * Used to support a fluent interface for adding onto a build item
	 * @var BuildItem
	 */
	protected $currentBuildItem = null;

	/**
	 * If you pass in a template path then the constructor will inherit the
	 * FileTemplates constructor which sets the template formmatter by default.
	 * Otherwise we will default to a text formatter. The reason for this is 
	 * to allow console apps to have composite templates without the need
	 * to use .phtml template files.
	 *
	 * @param	string				$filePath	relative path to template
	 * @param	PathFinderInterface $pathFinder	resolves the relative path
	 * @param	array				$data		data to be assigned
	 * @param	ViewFormmaterInterface	$formatter determines how to format
	 * @return	ViewCompositeTemplate
	 */
	public function __construct($templatePath = null,
								PathFinderInterface $finder = null,
								array $data = null,
								ViewFormatterInterface $formatter = null)
	{
		if (null !== $templatePath && is_string($templatePath)) {
			parent::__construct($templatePath, $data, $finder);
			if (null !== $formatter) {
				$this->setViewFormatter($formatter);
			}
			return;
		}

		if (null === $formatter) {
			$formatter = new Formatter\TextFormatter();
		}
		$this->setViewFormatter($formatter);

		if (null !== $data) {
			$this->load($data);
		}
	}

	/**
	 * @param	array	$data
	 * @param	bool	$isPrivate
	 * @return	string
	 */
	public function build(array $data = null, $isPrivate = false)
	{
		$err = 'Build failed:';
		$this->finalizeCurrentBuildItem();
		$buildItems = $this->getBuildItems();
		foreach ($buildItems as $buildItem) {
			$isSilentFail = $buildItem->isSilentFail();
			$sourceKey    = $buildItem->getSource();
			$assignLabel  = $buildItem->getAssignLabel();
			$targetKey    = $buildItem->getTarget();
			
			if (! $this->templateExists($sourceKey)) {
				if ($isSilentFail) {
					continue;
				}

				throw new Exception(
					"$err source ($sourceKey) template not found"
				);
			}
			$source = $this->getTemplate($sourceKey);
			$result = $source->build();
			if ($buildItem->isResultFilter()) {
				$filter = $buildItem->getResultFilter();
				/* An array with an object as the first param and the method
				 * as the second param passes as callable, we don't want the
				 * array
				 */
				if (is_callable($filter) && ! is_array($filter)) {
					$result = $filter($result);
				}
				else {
					$result = call_user_func($filter, $result);
				}
			}

			/* the target is this template */		
			if ('_this_' === $targetKey) {
				$this->assign($assignLabel, $result);
				continue;
			}
	
			if (! $this->templateExists($targetKey)) {
				if ($isSilentFail) {
					continue;
				}
				throw new Exception(
					"$err target ($targetKey) template not found"
				);
			}

			$target = $this->getTemplate($targetKey);
			$target->assign($assignLabel, $result);
		}
		return parent::build($data, $isPrivate);
	}

	/**
	 * Determines if template has been added
	 *
	 * @param	scalar	$key	template identifier
	 * @return	bool
	 */
    public function templateExists($key)
	{
		return array_key_exists($key, $this->templates) &&
				$this->templates[$key] instanceof ViewTemplateInterface;
	}

	/**
	 * @param	scalar				$key
	 * @param	TemplateInterface	$template
	 * @return	CompositeTemplate
	 */
	public function addTemplate($key, ViewTemplateInterface $template)
	{
		if (! is_scalar($key)) {
			throw new Exception("Invalid key: must be a scalar value");
		}

		$this->templates[$key] = $template;
		return $this;
	}

	/**
	 * @param	scalar	$key
	 * @return	TemplateInterface | false when no template is found
	 */
	public function getTemplate($key)
	{
		if (! $this->templateExists($key)) {
			return false;
		}

		return $this->templates[$key];
	}

	/**
	 * @param	scalar	$key	
	 * @return	CompositeTemplate
	 */	
	public function removeTemplate($key)
	{
		if (! $this->templateExists($key)) {
			return $this;
		}

		unset($this->templates[$key]);
		return $this;
	}

	/**
	 * @param	string		$templateKey
	 * @param	string		$label
	 * @param	mixed		$value
	 * @return	CompositeTemplate
	 */
	public function assignTo($key, $label, $value)
	{
		if (! $this->templateExists($key)) {
			return $this;
		}

		$this->getTemplate($key)
			 ->assign($label, $value);

		return $this;
	}

	/**
	 * This telss the build method to build the source template into the target
	 * template with the assignment label.
	 * 
	 * @param	string	$src		key for the source template
	 * @param	string	$assign		label used for assignment into target
	 * @param	string	$target		key for the target template
	 * @return	CompositeTemplate
	 */
	public function assignBuild($src, $label = null, $target = null)
	{
		$err = 'BuildTo failed:';
		if (! is_scalar($src) || empty($src)) {
			throw new Exception("$err  source must be non empty scalar");
		}

		/* when target template does not exist assume its this template */
		if (null === $target) {
			$target = '_this_';
		}
		
		/* when label does not exist then assume the src key as the label */
		if (null === $label) {
			$label = $src;
		}

		$this->finalizeCurrentBuildItem();

		$this->setCurrentBuildItem(
			$this->createBuildItem($src, $target, $label)
		);

		return $this;
	}

	/**
	 * Allows you to specify a callback function that will use the results of
	 * the build for the template decribed in the current build item
	 *
	 * @param	mixed	$function
	 * @return	CompositeTemplate
	 */
	public function filterResultsWith($function)
	{
		$this->validateCurrentBuildItem('filterResultsWith')
			 ->getCurrentBuildItem()
			 ->setResultFilter($function);
		
		return $this;
	}

	/**
	 * @return	CompositeTemplate
	 */
	public function letBuildFailSilently()
	{
		$this->validateCurrentBuildItem('letBuildFailSilently')
			 ->getCurrentBuildItem()
			 ->enableSilentFail();

		return $this;
	}

	/**
	 * @return	CompositeTemplate
	 */
	public function letBuildThrowException()
	{
		$this->validateCurrentBuildItem('letBuildThrowException')
			 ->getCurrentBuildItem()
			 ->disableSilentFail();

		return $this;
	}

	/**
	 * Push a build item on the stack
	 *
	 * @param	BuildItem	$item
	 * @return	CompositeTemplate
	 */
	public function addBuildItem(BuildItemInterface $item)
	{
		$this->buildItems[] = $item;
		return $this;
	}

	/**
	 * Return all the build items including the current build item if it has
	 * not be push onto the stack
	 *
	 * @return	array
	 */
	public function getBuildItems()
	{
		$result = $this->buildItems;
		if ($this->isCurrentBuildItem()) {
			$result[] = $this->getCurrentBuildItem();
		}
		return $result;
	}

	/**
	 * @param	string	$src
	 * @param	string	$target
	 * @param	string	$label
	 * @return	BuildItem
	 */
	public function createBuildItem($src, $target, $label)
	{
		return new BuildItem($src, $target, $label);
	}

	/**
	 * @return	BuildItemInterface
	 */
	protected function getCurrentBuildItem()
	{
		return $this->currentBuildItem;
	}

	/**
	 * @param	BuildItemInterface
	 * @return	CompositeTemplate
	 */
	protected function setCurrentBuildItem(BuildItemInterface $buildItem)
	{
		$this->currentBuildItem = $buildItem;
		return $this;
	}

	/**
	 * @return bool
	 */
	protected function isCurrentBuildItem()
	{
		return $this->currentBuildItem instanceof BuildItemInterface;
	}

	/**
	 * ensure if there is a current item that is push onto the stack and 
	 * then cleared off
	 *
	 * @return	CompositeTemplate
	 */
	protected function finalizeCurrentBuildItem()
	{
		if (! $this->isCurrentBuildItem()) {
			return $this;
		}

		$this->addBuildItem($this->getCurrentBuildItem());
		$this->currentItem = null;
		return $this;
	}

	/**
	 * When the current build does not exist throw an execption
	 * 
	 * @param	string	$method		name of the mehtod to use in error
	 * @return	CompositeTemplate
	 */
	protected function validateCurrentBuildItem($method)
	{
		if (! $this->isCurrentBuildItem()) {
			throw new Exception(
				"$method failed: 'fluent interface must use assignBuild first"
			);
		}

		return $this;
	}
}
