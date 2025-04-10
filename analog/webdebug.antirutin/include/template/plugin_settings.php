<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}
	
$arPlugin = &$arParams['PLUGIN_ARRAY'];
if(!is_array($arPlugin)){
	return;
}

$obPlugin = &$arParams['PLUGIN_OBJECT'];
if(!is_object($obPlugin)){
	return;
}

if(is_array($arParams['SAVED_DATA'])){
	$obPlugin->setSavedData($arParams['SAVED_DATA']);
}

$strCode = $arPlugin['CODE'];

$strId = $strCode.'_'.time();
$strId = $obPlugin->getId();

$arSavedData = $obPlugin->getSavedData();

$strTitle = strlen($arSavedData['_title']) ? $arSavedData['_title'] : $arPlugin['NAME'];
$bCollapsed = $arSavedData['_collapsed'] == 'Y';
$strSort = $arSavedData['_sort'];

$bHasDescription = !!strlen($obPlugin::getDescription());
?>
<div class="plugin-settings" data-role="plugin-settings" data-plugin="<?=$strCode;?>" id="plugin-settings-<?=$strId;?>"
	data-id="<?=$strId;?>">
	<div class="wda-fieldset<?if($bCollapsed):?> wda-fieldset-collapsed<?endif?>" data-role="wda-fieldset">
		<div class="wda-fieldset-inner">
			<div class="wda-fieldset-legend" title="<?=htmlspecialcharsbx($arPlugin['HINT']);?>">
				<?if(strlen($arPlugin['ICON_BASE64'])):?>
					<span class="wda-fieldset-legend-icon">
						<img src="<?=$arPlugin['ICON_BASE64'];?>" alt="" />
					</span>
				<?endif?>
				<a href="#toggle" class="wda-fieldset-legend-title" data-role="wda-fieldset-toggle">
					<?=htmlspecialcharsbx($strTitle);?>
				</a>
			</div>
			<div class="wda-fieldset-control">
				<?if($bHasDescription):?>
					<a href="#help" class="plugin-settings__help" data-role="plugin-settings-help">
						<span>
							<span>
								<?=Helper::getMessage('WDA_FILE_PLUGIN_SETTINGS_HELP');?>
							</span>
						</span>
					</a>
				<?endif?>
				<a href="#rename" class="plugin-settings__rename" data-role="plugin-settings-rename"
					data-prompt="<?=Helper::getMessage('WDA_FILE_PLUGIN_SETTINGS_RENAME_PROMPT');?>">
					<span>
						<span>
							<?=Helper::getMessage('WDA_FILE_PLUGIN_SETTINGS_RENAME');?>
						</span>
					</span>
				</a>
				<a href="#pin" class="plugin-settings__pin" data-role="plugin-settings-pin"
					data-pin-plugins-to-end="<?=(Helper::getOption('pin_plugins_to_end') == 'Y' ? 'Y' : 'N');?>">
					<span>
						<span>
							<?=Helper::getMessage('WDA_FILE_PLUGIN_SETTINGS_PIN');?>
						</span>
					</span>
				</a>
				<a href="#unpin" class="plugin-settings__unpin" data-role="plugin-settings-unpin"
					data-confirm="<?=Helper::getMessage('WDA_FILE_PLUGIN_SETTINGS_UNPIN_CONFIRM', 
					['#TITLE#' => $arPlugin['NAME']]);?>">
						<span>
							<span>
								<?=Helper::getMessage('WDA_FILE_PLUGIN_SETTINGS_UNPIN');?>
							</span>
						</span>
				</a>
			</div>
			<input type="hidden" name="<?=$obPlugin->getInputName('_plugin');?>" data-role="fieldset-plugin"
				value="<?=$obPlugin->getCode();?>" />
			<input type="hidden" name="<?=$obPlugin->getInputName('_title');?>" data-role="fieldset-title"
				value="<?=htmlspecialcharsbx($strTitle)?>" />
			<input type="hidden" name="<?=$obPlugin->getInputName('_collapsed');?>" data-role="fieldset-collapsed"
				value="<?=($bCollapsed?'Y':'N');?>" />
			<input type="hidden" name="<?=$obPlugin->getInputName('_sort');?>" data-role="fieldset-sort"
				value="<?=$strSort;?>" />
			<div class="wda-fieldset-content" data-role="wda-fieldset-content" data-disabling-control="true">
				<?if($obPlugin->isSingleTime()):?>
					<?=Helper::showNote(Helper::getMessage('WDA_FILE_PLUGIN_SETTINGS_SINGLE_TIME'), true);?>
					<br/>
				<?endif?>
				<?=$obPlugin->showSettings();?>
			</div>
		</div>
	</div>
	<script>$('#<?=$strId;?>').trigger('pluginload');</script>
</div>