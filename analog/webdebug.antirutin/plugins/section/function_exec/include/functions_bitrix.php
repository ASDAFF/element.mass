<?
namespace WD\Antirutin;

Helper::loadMessages(__FILE__);

return [
	'htmlspecialcharsbx' => [
		'VALUE' => true,
		'FLAGS' => [
			'REQUIRED' => false,
			'DEFAULT' => ENT_COMPAT,
			'DEFAULT_TEXT' => 'ENT_COMPAT',
		],
	],
	'toLower' => [
		'VALUE' => true,
		'LANG' => [
			'REQUIRED' => false,
			'DEFAULT' => '#FALSE#',
			'DEFAULT_TEXT' => 'false',
		],
	],
	'toUpper' => [
		'VALUE' => true,
		'LANG' => [
			'REQUIRED' => false,
			'DEFAULT' => '#FALSE#',
			'DEFAULT_TEXT' => 'false',
		],
	],
];
