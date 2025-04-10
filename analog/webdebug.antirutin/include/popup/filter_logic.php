<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\Filter;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

# Offers?
if($bOffers){
	$arParams['IBLOCK_ID_MAIN'] = $arParams['IBLOCK_ID'];
	$arParams['IBLOCK_ID'] = $arParams['IBLOCK_OFFERS_ID'];
}

# Create filter
$obFilter = new Filter($arParams['IBLOCK_ID'], $arParams['ENTITY_TYPE']);

# Get all available fields
$arAvailableElementFieldsPlain = IBlock::getAvailableFieldsPlain($arParams['IBLOCK_ID'], $arParams['ENTITY_TYPE']);

# 
$strType = '';
$arCurrentField = $arAvailableElementFieldsPlain[$strCurrentField];
$arLogicAll = array();
if(is_array($arCurrentField)) {
	$strType = $arCurrentField['TYPE'];
	$strUserType = $arCurrentField['USER_TYPE'];
	if(strlen($strType)){
		$arLogicAll = $obFilter->getLogicAll($strType, $strUserType, $strCurrentField);
	}
}

?>
<div class="wda-form">
	<?if(strlen($strType)):?>
		<input type="hidden" data-role="allow-save" />
		<table class="wda-field-select-table">
			<tbody>
				<tr>
					<td>
						<input type="text" value="" class="wda-field-select-text" data-role="entity-select-search"
							placeholder="<?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_TEXT_PLACEHOLDER');?>"/>
					</td>
				</tr>
				<tr>
					<td>
						<select class="wda-field-select-list" size="10" data-role="entity-select-item">
							<option value="" disabled="disabled" data-role="entity-select-item-not-found">
								<?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_NOT_FOUND');?>
							</option>
							<?foreach($arLogicAll as $strLogic => $arLogic):?>
								<option value="<?=$strLogic;?>" <?if($strLogic==$strCurrentLogic):?> selected="selected"<?endif?>
									<?if($arLogic['HIDE_VALUE']):?>data-hide-value="Y"<?endif?>
									><?
									print $arLogic['NAME'];
								?></option>
							<?endforeach?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	<?else:?>
		<div><?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_NO_FIELD');?></div>
	<?endif?>
</div>
