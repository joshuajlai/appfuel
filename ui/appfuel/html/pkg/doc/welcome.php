<?php
return array(
	'doc-type'		  => 'html5',
	'html-attrs'	  => array('lang' => 'en'),
	'html-head-attrs' => array('id' => 'my-html-head'),
	'html-head-title' => 'Welcome To The Appfuel Framework',
	'html-head-meta-equiv'  => array(
		array('X-UA-Compatible', 'IE=Edge,chrome=1'),
		array('X-XRDS-Location', '/xrds.xml') 
	),
	'html-head-meta-name' => array(
		array('php framework', 'appfuel framework'),
		array('author', 'Robert Scott-Buccleuch')
	),
	'html-head-favicon' => array('image/png', '/img/favicon.png'),
	'html-body-class' => array('class' => "yui3-skin-sam")
);
?>
