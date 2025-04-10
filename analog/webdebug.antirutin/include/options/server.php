<?
namespace WD\Antirutin;

use \WD\Antirutin\Helper;

Helper::loadMessages(__FILE__);
$strLang = 'WDA_OPTIONS_';
$strHint = $strLang.'HINT_';

return [
	'NAME' => Helper::getMessage($strLang.'OPTION_SERVER'),
	'OPTIONS' => [
		'php_path' => [
			'NAME' => Helper::getMessage($strLang.'OPTION_PHP_PATH'),
			'HINT' => Helper::getMessage($strHint.'OPTION_PHP_PATH'),
			'TYPE' => 'text',
			'ATTR' => 'size="30"',
		],
		'php_config' => [
			'NAME' => Helper::getMessage($strLang.'OPTION_PHP_CONFIG'),
			'HINT' => Helper::getMessage($strHint.'OPTION_PHP_CONFIG'),
			'TYPE' => 'text',
			'ATTR' => 'size="90"',
		],
		'php_mbstring' => [
			'NAME' => Helper::getMessage($strLang.'OPTION_PHP_MBSTRING'),
			'HINT' => Helper::getMessage($strHint.'OPTION_PHP_MBSTRING'),
			'TYPE' => 'checkbox',
			'ATTR' => '',
		],
		'php_output_stdout' => [
			'NAME' => Helper::getMessage($strLang.'OPTION_PHP_OUTPUT_STDOUT'),
			'HINT' => Helper::getMessage($strHint.'OPTION_PHP_OUTPUT_STDOUT'),
			'TYPE' => 'checkbox',
			'ATTR' => '',
		],
	],
];
?>