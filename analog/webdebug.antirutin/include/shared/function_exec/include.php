<?
namespace WD\Antirutin;

Helper::loadMessages(__FILE__);

$arResult = [
	'PHP' => require __DIR__.'/functions_php.php',
	'BITRIX' => require __DIR__.'/functions_bitrix.php',
	'WEBDEBUG' => require __DIR__.'/functions_webdebug.php',
	/* ToDo!
	'CUSTOM' => [],
	'MISC' => [
		'_custom_' => [],
	],
	*/
];
foreach($arResult['WEBDEBUG'] as $strFunc => &$arFunc){
	if(!strlen($arFunc['__NAMESPACE'])){
		$arFunc['__NAMESPACE'] = '\\WD\\Antirutin\\';
	}
}
unset($arFunc);
ksort($arResult['PHP']);
ksort($arResult['BITRIX']);
ksort($arResult['WEBDEBUG']);
foreach($arResult as $strGroup => $arGroup){
	$arGroup = [
		'NAME' => static::getMessage('GROUP_'.$strGroup),
		'ITEMS' => $arGroup,
	];
	foreach($arGroup['ITEMS'] as $strFunc => $arFunc){
		$mCallback = null;
		if(array_key_exists('__CALLBACK', $arFunc)){
			$mCallback = $arFunc['__CALLBACK'];
			unset($arFunc['__CALLBACK']);
		}
		$strNamespace = '';
		if(array_key_exists('__NAMESPACE', $arFunc)){
			$strNamespace = $arFunc['__NAMESPACE'];
			unset($arFunc['__NAMESPACE']);
		}
		foreach($arFunc as $strArg => &$arArg){
			$strArg = toUpper($strArg);
			$bRequired = is_bool($arArg) ? $arArg : $arArg['REQUIRED'] === true;
			$arArg = is_array($arArg) ? $arArg : [];
			$arArg['IS_FIELD'] = $strArg == 'VALUE';
			$arArg['REQUIRED'] = $bRequired;
			if(!$arArg['IS_FIELD']){
				$arArg['NAME'] = static::getMessage($strFunc.'__'.$strArg);
				$arArg['DEFAULT'] = array_key_exists('DEFAULT', $arArg) ? $arArg['DEFAULT'] : null;
				if(!strlen($arArg['DEFAULT_TEXT'])){
					$arArg['DEFAULT_TEXT'] = var_export($arArg['DEFAULT'], true);
				}
				if(!is_null($strPlaceholder = static::getMessage($strFunc.'__'.$strArg.'_PLACEHOLDER'))){
					$arArg['PLACEHOLDER'] = $strPlaceholder;
				}
				$arArg['HINT'] = is_string($arArg['HINT']) ? $arArg['HINT'] : '';
				if(!is_null($strHint = static::getMessage($strFunc.'__'.$strArg.'__HINT'))){
					$arArg['HINT'] = $strHint;
				}
				if(!$arArg['REQUIRED']){
					if(strlen($arArg['HINT'])){
						$arArg['HINT'] .= str_repeat('<br/>', 2);
					}
					$arArg['HINT'] .= static::getMessage('ARG_HINT_DEFAULT_VALUE', [
						'#VALUE#' => is_string($arArg['DEFAULT_TEXT']) ? $arArg['DEFAULT_TEXT'] 
							: var_export($arArg['DEFAULT'], true),
					]);
				}
			}
		}
		unset($arArg);
		$arGroup['ITEMS'][$strFunc] = [
			'NAME' => static::getMessage($strFunc),
			'FUNC' => $strFunc,
			'ARGS' => $arFunc,
		];
		if(strlen($strNamespace)){
			$arGroup['ITEMS'][$strFunc]['NAMESPACE'] = $strNamespace;
		}
		if(is_callable($mCallback)){
			$arGroup['ITEMS'][$strFunc]['CALLBACK'] = $mCallback;
		}
	}
	$arResult[$strGroup] = $arGroup;
}
if($bNoGroups){
	$arResultTmp = [];
	foreach($arResult as $strGroup => $arGroup){
		if(is_array($arGroup['ITEMS'])){
			$arResultTmp = array_merge($arResultTmp, $arGroup['ITEMS']);
		}
	}
	$arResult = $arResultTmp;
}
return $arResult;