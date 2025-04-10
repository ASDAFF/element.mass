<?
namespace WD\Antirutin;

Helper::loadMessages(__FILE__);

return [
	'htmlspecialcharsbx' => [
		'VALUE' => true,
		'FLAGS' => [
			'REQUIRED' => false,
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(isset($arArgs[1])){
				$arArgs[1] = Helper::evalReturn($arArgs[1]);
			}
		}
	],
	'htmlspecialcharsback' => [
		'VALUE' => true,
	],
	'toLower' => [
		'VALUE' => true,
		'LANG' => [
			'REQUIRED' => false,
			'DEFAULT' => 'false',
			'DEFAULT_TEXT' => 'false',
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(isset($arArgs[1])){
				$arArgs[1] = Helper::evalReturn($arArgs[1]);
			};
		}
	],
	'toUpper' => [
		'VALUE' => true,
		'LANG' => [
			'REQUIRED' => false,
			'DEFAULT' => 'false',
			'DEFAULT_TEXT' => 'false',
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(isset($arArgs[1])){
				$arArgs[1] = Helper::evalReturn($arArgs[1]);
			};
		}
	],
	'roundEx' => [
		'VALUE' => true,
		'PRECISION' => [],
	],
	'utf8win1251' => [
		'VALUE' => true,
	],
	'truncateText' => [
		'VALUE' => true,
		'LENGTH' => [
			'REQUIRED' => true,
		],
	],
	'txtToHtml' => [
		'VALUE' => true,
		'MAKE_URLS' => [
			'DEFAULT' => 'true',
		],
		'MAX_LENGTH' => [
			'DEFAULT' => '0',
		],
		'QUOTE_ENABLED' => [
			'DEFAULT' => 'N',
		],
		'NOT_CONVERT_AMPERSAND' => [
			'DEFAULT' => 'Y',
		],
		'CODE_ENABLED' => [
			'DEFAULT' => 'N',
		],
		'BIU_ENABLED' => [
			'DEFAULT' => 'N',
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(isset($arArgs[1])){
				$arArgs[1] = Helper::evalReturn($arArgs[1]);
			};
		}
	],
	'htmlToTxt' => [
		'VALUE' => true,
		'SITE_URL' => [],
		'DELETE' => [],
		'LENGTH' => [
			'DEFAULT' => '70',
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(isset($arArgs[3])){
				$arArgs[3] = Helper::evalReturn($arArgs[3]);
			}
		}
	],
	'CTextParser::closeTags' => [
		'VALUE' => true,
	],
	'CUtil::closeTags' => [
		'VALUE' => true,
	],
	'CUtil::translit' => [
		'VALUE' => true,
		'LANG' => [
			'REQUIRED' => true,
			'DEFAULT' => LANGUAGE_ID,
		],
		'PARAMS' => [
			'DEFAULT' => 'array("max_len"=>255, "change_case"=>"L", "replace_space"=>"-", "replace_other" => "-", "delete_repeat_replace" => true)',
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(isset($arArgs[2])){
				$arArgs[2] = Helper::evalReturn($arArgs[2]);
			}
		}
	],
	'normalizePhone' => [
		'VALUE' => true,
		'MIN_LENGTH' => [
			'DEFAULT' => '10',
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(isset($arArgs[1])){
				$arArgs[1] = intVal($arArgs[1]);
			}
		}
	],
	'escapePhpString' => [
		'VALUE' => true,
		'ENCLOSER' => [
			'DEFAULT' => '"',
		],
	],
	'unEscapePhpString' => [
		'VALUE' => true,
		'ENCLOSER' => [
			'DEFAULT' => '"',
		],
	],
	'randString' => [
		'LENGTH' => [],
		'PASS_CHARS' => [],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(is_null($arArgs[0])){
				$arArgs[0] = 10;
			}
			if(is_null($arArgs[1])){
				$arArgs[1] = false;
			}
		}
	],
];
