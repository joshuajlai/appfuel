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

return array(
	'html-head' => array(
		'title' => array('text' => 'Appfuel Framework', 'sep' => ' '),
		'base'  => array('href' => 'some-url.com'),
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
		'css-links' => array(
			'http://yui.yahooapis.com/3.4.1/build/cssreset/cssreset-min.css',
			'http://yui.yahooapis.com/3.4.1/build/cssgrids/grids-min.css',
		),
	),

	'html-body' => array(
		'js-scripts' => array(
			'http://yui.yahooapis.com/3.4.1/build/yui/yui-min.js',
		),

	)
);
