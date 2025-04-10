<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\ProfileTable as Profile;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$intProfileId = &$arParams['PROFILE_ID'];
$arPluginsPlain = &$arParams['PLUGINS_PLAIN'];

if(is_numeric($intProfileId)){
	$arProfile = Helper::getProfileArray($intProfileId);
	if(is_array($arProfile)){
		$arProfile['ACTIONS'] = Profile::getProfileActions($intProfileId);
		foreach($arProfile['ACTIONS'] as $strHash => $arAction){
			$strActionPlugin = $arAction['PLUGIN'];
			$arActionPlugin = $arPluginsPlain[$strActionPlugin];
			if(is_array($arActionPlugin)){
				$obActionPlugin = new $arActionPlugin['CLASS'];
				$obActionPlugin->setEntityType($arParams['ENTITY_TYPE']);
				$obActionPlugin->setId($strHash);
				$obActionPlugin->setIBlockId($arProfile['IBLOCK_ID']);
				print Helper::includeFile('plugin_settings', [
					'IBLOCK_ID' => $arProfile['IBLOCK_ID'],
					'PLUGIN_CODE' => $strActionPlugin,
					'PLUGIN_ARRAY' => $arActionPlugin,
					'PLUGIN_OBJECT' => $obActionPlugin,
					'SAVED_DATA' => $arAction['PARAMS'],
				]);
			}
		}
	}
}
?>