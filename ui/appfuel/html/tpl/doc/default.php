<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
use Appfuel\View\Html\Resource\ResourceManager;

$pkg = ResourceManager::getResourcePackage();

return array(
	'html-head' => array(
		'title' => array('text' => 'Appfuel Framework', 'sep' => ' '),
		'base'  => array('href' => $pkg->getBaseUrl()),
		'meta'  => array(
			array(
				'content'	 => 'text/html', 
				'http-equiv' => 'Content-Type',
				'charset'	 => 'UTF-8'
			),
			array(
				'name'	  => 'framework', 
				'content' => 'appfuel'
			)
		),
		'css-files' => $pkg->getCssFiles(array('framework', 'global-app')),
	),
	'html-body' => array(
		'js-files' => $pkg->getJsFiles(array('framework', 'global-app'))
	)
);
