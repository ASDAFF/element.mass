<?
namespace WD\Antirutin\IBlock;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strId = strlen($arParams['ID']) ? $arParams['ID'].'_available_fields' : Helper::randString();

$arValue = is_array($arParams['VALUE']) ? $arParams['VALUE'] : (strlen($arParams['VALUE']) ? [$arParams['VALUE']] : []);

if(is_string($arParams['DEFAULT']) && !strlen($arParams['DEFAULT']) || $arParams['DEFAULT'] === true){
	$arParams['DEFAULT'] = Helper::getMessage('WDA_FILE_AVAILABLE_FIELDS_NO_VALUE');
}
?>
<div id="<?=$strId;?>" data-role="wda_available_fields">
	<select name="<?=$arParams['INPUT_NAME'];?>" <?=$arParams['ATTR'];?>>
		<?if(strlen($arParams['DEFAULT'])):?>
			<option value=""><?=$arParams['DEFAULT'];?></option>
		<?endif?>
		<?foreach($arParams['FIELDS'] as $strGroup => $arGroup):?>
			<?if(!empty($arGroup['ITEMS'])):?>
				<optgroup label="<?=htmlspecialcharsbx($arGroup['NAME']);?>" data-code="<?=$strGroup;?>">
					<?foreach($arGroup['ITEMS'] as $strItem => $arItem):?>
						<option value="<?=$arGroup['PREFIX'].$strItem;?>"
							data-multiple="<?=($arItem['IS_MULTIPLE'] == 'Y' ? 'Y' : 'N');?>" data-type="<?=$arItem['TYPE_FULL'];?>"
							<?if(in_array($arGroup['PREFIX'].$strItem, $arValue)):?>selected="selected"<?endif?>><?
							print IBlock::displayAvailableItemName($arItem, false, !!$arGroup['OFFERS']);
							?></option>
					<?endforeach?>
				</optgroup>
			<?endif?>
		<?endforeach?>
	</select>
</div>
<?if($arParams['WITHOUT_SELECT2'] !== true):?>
	<script>
	wdaSelect2($('#<?=$strId;?> > select'), {
		dropdownParent: $('#<?=$strId;?>'),
		<?if($arParams['PLACEHOLDER']):?>
			placeholder: '<?=Helper::getMessage('WDA_FILE_AVAILABLE_FIELDS_PLACEHOLDER');?>',
		<?endif?>
	});
	</script>
<?endif?>