<?php
return array(
	'css-scriptname' => 'appfuel-global',
	'css-modules' => array(
		'css/module/base'
	),
	'js-scriptname' => 'appfuel-global',

	'js-files' => array(
		'js/module/kernel/special/one-off-file.js',
	),

	'js-modules' => array(
		'js/module/kernel/kernel-core',
		'js/module/kernel/kernel-io',
		'js/module/kernel/kernel-mvc',
	),

	'asset-files' => array(
		'asset/icons/icon-spirite.png',
		'asset/icons/favicon.png',
		'asset/logos/appfuel-logo.jpg',
	),

	'asset-modules' => array(
		'asset/global',
	),

	'depends' => array(
		'yui3/pkg/yui-core.php',
	)
);
