<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package 	Appfuel
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace 	Appfuel\StdLib\Config;

use Appfuel\StdLib\Filesystem\File as File;

/**
 *
 * @package 	Appfuel
 */
interface BuilderInterface
{
	
	/**
	 * @return	BuilderInterface
	 */
	public function	inherit($section = 'default');

	/**
	 * @return	string
	 */	
	public function getInheritSection();
	
	/**
	 * @return	BuilderInterface
	 */
	public function	disableInheritance();

	/**
	 * @return	BuilderInterface
	 */
	public function	enableInheritance();

	/**
	 * @return	BuilderInterface
	 */
	public function	setInheritanceFlag($flag);

	/**
	 * @return	BuilderInterface
	 */
	public function	isInheritance();

    /**
     * @return  AdapterInterface
     */
    public function getFileAdapter();

    /**
     * @return  BuilderInterface
     */
    public function setFileAdapter(Adapter\AdapterInterface $adapter);

    /**
     * @return  string
     */
    public function getFileStrategy();

    /**
     * @return  Builder
     */
    public function setFileStrategy($fileType);
	
	/**
	 * @return	AfList\Basic
	 */
	public function build(File $file);
}

