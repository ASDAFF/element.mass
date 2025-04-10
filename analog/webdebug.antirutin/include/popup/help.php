<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strLang = 'WDA_POPUP_HELP_';

$strEntity = $arParams['POST']['help_entity'];
$strPlugin = $arParams['POST']['help_plugin'];

$strDescription = '';
if(strlen($strEntity)){
	$arPlugins = Helper::findPlugins(toUpper($strEntity), $bGroup=false);
	if(is_array($arPlugins) && is_array($arPlugins[$strPlugin])){
		$strClass = $arPlugins[$strPlugin]['CLASS'];
		$obPlugin = new $strClass;
		$strDescription = $obPlugin->getDescription();
		$arParams['JSON']['PluginName'] = $obPlugin->getName();
	}
}

?>
<div class="wda-form">
	<div class="help__form">
		<?if(strlen($strDescription)):?>
			<?=$strDescription;?>
		<?else:?>
			<?=Helper::getMessage($strLang.'NO_DESCRIPTION');?>
		<?endif?>
	</div>
</div>
