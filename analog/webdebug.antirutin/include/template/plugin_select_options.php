<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}
	
$arPluginsGrouped = Helper::findPlugins($arParams['ENTITY_TYPE'], $bGroup=true);

?>
<option value=""><?=Helper::getMessage('WDA_HEADER_ACTIONS_PLACEHODER');?></option>
<?foreach($arPluginsGrouped as $strGroup => $arGroup):?>
	<optgroup label="<?=(strlen($arGroup['NAME']) ? $arGroup['NAME'] : $strGroup);?>">
		<?foreach($arGroup['ITEMS'] as $strPlugin => $arPlugin):?>
			<option value="<?=$arPlugin['CODE'];?>" data-hint="<?=htmlspecialcharsbx($arPlugin['HINT']);?>" 
				<?if($arParams['PLUGIN_CODE'] == $strPlugin):?>selected="selected"<?endif?>
				data-icon="<?=$arPlugin['ICON_BASE64'];?>"><?
				print $arPlugin['NAME'];
			?></option>
		<?endforeach?>
	</optgroup>
<?endforeach?>