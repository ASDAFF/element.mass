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

# Create filter
$obFilter = new Filter($arParams['IBLOCK_ID'], $arParams['ENTITY_TYPE']);

# Get all available fields
$arAvailableElementFields = IBlock::getAvailableFields($arParams['IBLOCK_ID'], $arParams['ENTITY_TYPE']);
foreach($arAvailableElementFields as $strGroup => $arGroup){
	if(is_array($arGroup['ITEMS'])) {
		foreach($arGroup['ITEMS'] as $key => $arItem){
			if($arItem['IS_FILTRABLE'] !== 'Y'){
				unset($arAvailableElementFields[$strGroup]['ITEMS'][$key]);
			}
		}
	}
	if(empty($arGroup['ITEMS'])){
		unset($arAvailableElementFields[$strGroup]);
	}
}

# Get all available fields (offers)
if($arParams['IBLOCK_OFFERS_ID'] && $arParams['ENTITY_TYPE'] == Helper::TYPE_ELEMENT){
	$arAvailableOfferFields = IBlock::getAvailableFields($arParams['IBLOCK_OFFERS_ID'], $arParams['ENTITY_TYPE']);
	foreach($arAvailableOfferFields as $strGroup => $arGroup){
		if(is_array($arGroup['ITEMS'])) {
			foreach($arGroup['ITEMS'] as $key => $arItem){
				if($arItem['IS_FILTRABLE'] !== 'Y'){
					unset($arAvailableOfferFields[$strGroup]['ITEMS'][$key]);
				}
			}
		}
		if(empty($arGroup['ITEMS'])){
			unset($arAvailableOfferFields[$strGroup]);
		}
	}
}

$strIBlockType = htmlspecialcharsbx($arPost['iblock_type']);

?>
<div class="wda-form">
	<input type="hidden" data-role="allow-save" />
	<?if(!$arParams['IBLOCK_OFFERS_ID']):?>
		<input type="hidden" data-role="entity-select-type-hidden" value="main" />
	<?endif?>
	<table class="wda-field-select-table">
		<tbody>
			<tr>
				<?if($arParams['IBLOCK_OFFERS_ID'] && $arParams['ENTITY_TYPE'] == Helper::TYPE_ELEMENT):?>
					<td>
						<select class="wda-field-select-type" data-role="entity-select-type">
							<option value="main"<?if(!$bOffers):?> selected="selected"<?endif?>>
								<?=Helper::getMessage('WDA_FILTER_POPUP_TYPE_MAIN');?>
							</option>
							<option value="offers"<?if($bOffers):?> selected="selected"<?endif?>>
								<?=Helper::getMessage('WDA_FILTER_POPUP_TYPE_OFFERS');?>
							</option>
						</select>
					</td>
				<?endif?>
				<td>
					<input type="text" value="" class="wda-field-select-text" data-role="entity-select-search" placeholder="<?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_TEXT_PLACEHOLDER');?>"/>
				</td>
			</tr>
			<tr>
				<td<?if($arParams['IBLOCK_OFFERS_ID'] && $arParams['ENTITY_TYPE'] == Helper::TYPE_ELEMENT):?> colspan="2"<?endif?>>
					<select class="wda-field-select-list" size="10" data-role="entity-select-item" data-type="main">
						<option value="" disabled="disabled" data-role="entity-select-item-not-found" style="display:none">
							<?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_NOT_FOUND');?>
						</option>
						<?foreach($arAvailableElementFields as $strGroup => $arGroup):?>
							<?if(is_array($arGroup['ITEMS']) && !empty($arGroup['ITEMS'])):?>
								<optgroup label="<?=$arGroup['NAME'];?>" data-code="<?=$strGroup;?>">
									<?foreach($arGroup['ITEMS'] as $strItem => $arItem):?>
										<?
										$strItemCode = strlen($arGroup['PREFIX']) ? $arGroup['PREFIX'].$strItem : $strItem;
										if(in_array($strItemCode, ['DETAIL_PAGE_URL'])){
											continue;
										}
										$arItem['NAME_PREFIX'] = $arGroup['NAME_PREFIX'];
										$arLogic = $obFilter->getLogicAll($arItem['TYPE'], $arItem['USER_TYPE'], $strItemCode);
										$arLogic = array_merge(array(
											'CODE' => key($arLogic),
										), reset($arLogic));
										?>
										<option value="<?=$strItemCode;?>" data-name="<?=htmlspecialcharsbx($arItem['NAME']);?>" 
											<?if($strItemCode==$strCurrentField):?> selected="selected"<?endif?>
											data-logic-code="<?=$arLogic['CODE'];?>"
											data-logic-name="<?=$arLogic['NAME'];?>"
											data-logic-hide-value="<?=($arLogic['HIDE_VALUE'] ? 'Y' : 'N');?>"
										><?
											print IBlock::displayAvailableItemName($arItem);
										?></option>
									<?endforeach?>
								</optgroup>
							<?endif?>
						<?endforeach?>
					</select>
					<?if($arParams['IBLOCK_OFFERS_ID'] && $arParams['ENTITY_TYPE'] == Helper::TYPE_ELEMENT):?>
						<select class="wda-field-select-list" size="10" data-role="entity-select-item" data-type="offers" style="display:none">
							<option value="" disabled="disabled" data-role="entity-select-item-not-found" style="display:none">
								<?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_NOT_FOUND');?>
							</option>
							<?foreach($arAvailableOfferFields as $strGroup => $arGroup):?>
								<?if(is_array($arGroup['ITEMS']) && !empty($arGroup['ITEMS'])):?>
									<optgroup label="<?=$arGroup['NAME'];?>" data-code="<?=$strGroup;?>">
										<?foreach($arGroup['ITEMS'] as $strItem => $arItem):?>
											<?
											$strItemCode = strlen($arGroup['PREFIX']) ? $arGroup['PREFIX'].$strItem : $strItem;
											$arItem['NAME_PREFIX'] = $arGroup['NAME_PREFIX'];
											$arLogic = $obFilter->getLogicAll($arItem['TYPE'], $arItem['USER_TYPE'], $strItemCode);
											$arLogic = array_merge(array(
												'CODE' => key($arLogic),
											), reset($arLogic));
											$strTitle = Helper::getMessage('WDA_IBLOCK_FIELD_FIELD__PREFIX_OFFER')
												.htmlspecialcharsbx($arItem['NAME']);
											?>
											<option value="<?=$strItemCode;?>" data-name="<?=$strTitle;?>" 
												<?if($strItemCode==$strCurrentField):?> selected="selected"<?endif?>
												data-logic-code="<?=$arLogic['CODE'];?>"
												data-logic-name="<?=$arLogic['NAME'];?>"
												data-logic-hide-value="<?=($arLogic['HIDE_VALUE'] ? 'Y' : 'N');?>"
											><?
												print IBlock::displayAvailableItemName($arItem, false, true);
											?></option>
										<?endforeach?>
									</optgroup>
								<?endif?>
							<?endforeach?>
						</select>
					<?endif?>
				</td>
			</tr>
		</tbody>
	</table>
</div>