<?php

return array(
	'validators' => array(
		'single-field' => 'Appfuel\Validate\SingleFieldValidator',
	),
	'filters' => array(
		'int'		=> 'Appfuel\Validate\Filter\IntFilter',
		'bool'		=> 'Appfuel\Validate\Filter\BoolFilter',
		'string'	=> 'Appfuel\Validate\Filter\StringFilter',
		'email'     => 'Appfuel\Validate\Filter\EmailFilter',
		'regex'		=> 'Appfuel\Validate\Filter\RegexFilter',
		'ip'		=> 'Appfuel\Validate\Filter\IpFilter',
		'float'		=> 'Appfuel\Validate\Filter\FloatFilter',
	)
);
