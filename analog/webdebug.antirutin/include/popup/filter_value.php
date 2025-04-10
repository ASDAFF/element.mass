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
$arAvailableElementFields = IBlock::getAvailableFieldsPlain($arParams['IBLOCK_ID'], $arParams['ENTITY_TYPE']);

#
$bSection = $arParams['ENTITY_TYPE'] == Helper::TYPE_SECTION;

#
$strType = null;
$arCurrentField = $arAvailableElementFields[$strCurrentField];
$arCurrentLogic = null;
if(is_array($arCurrentField)) {
	$strType = $arCurrentField['TYPE'];
	$strUserType = $arCurrentField['USER_TYPE'];
	if(strlen($strType) && strlen($strCurrentLogic)){
		$arLogicAll = $obFilter->getLogicAll($strType, $strUserType, $strCurrentField);
		$arCurrentLogic = $arLogicAll[$strCurrentLogic];
	}
}

// Text for value search
$strValueSearch = $arPost['q'];

# Helpers for select items via AJAX
$bAjaxSelectItems = true;
$strCustomAction = $arGet['custom_action'];
$intResultsPerPage = 30;
$intPage = IntVal($arPost['page']) == 0 ? 1 : IntVal($arPost['page']); // 1, 2, ...
$intIndex = 0;
$intIndexMin = ($intPage - 1) * $intResultsPerPage + 1;
$intIndexMax = ($intPage) * $intResultsPerPage;
$arJsonItems = array();
$intCount = 0;
$bIncompleteResults = false;

// Load enums for PROPERTY_TYPE = L
if($strCustomAction=='load_items_l'){
	if($bSection){
		$arItems = IBlock::getPropertyEnums($arCurrentField['DATA']['FIELD_NAME'], $arParams['IBLOCK_ID']);
		foreach($arItems as $key => $arEnum){
			$intIndex++;
			if($intIndex >= $intIndexMin && $intIndex <= $intIndexMax){
				$arJsonItems[] = array(
					'id' => IntVal($arEnum['ID']),
					'text' => $arEnum['VALUE'].' ['.$arEnum['ID'].']',
				);
			}
		}
	}
	else{
		$intIBlockItemsID = IntVal($arCurrentField['DATA']['IBLOCK_ID']);
		if($intIBlockItemsID>0) {
			$arFilter = array(
				'PROPERTY_ID' => $arCurrentField['DATA']['ID'],
			);
			if(strlen($strValueSearch)){
				$arFilter[] = array(
					'LOGIC' => 'OR',
					array('ID' => $strValueSearch),
					array('%VALUE' => $strValueSearch),
				);
			}
			$resProps = \Bitrix\Iblock\PropertyEnumerationTable::getList(array(
				'filter' => $arFilter,
				'order' => array('VALUE' => 'ASC'),
			));
			while($arItem = $resProps->fetch()){
				$intIndex++;
				if($intIndex >= $intIndexMin && $intIndex <= $intIndexMax){
					$arJsonItems[] = array(
						'id' => IntVal($arItem['ID']),
						'text' => $arItem['VALUE'].' ['.$arItem['ID'].']',
					);
				}
			}
		}
	}
	$intCount = $intIndex;
}
// Load enums for PROPERTY_TYPE = E
elseif($strCustomAction=='load_items_e'){
	$intIBlockItemsID = IntVal($arCurrentField['DATA']['LINK_IBLOCK_ID']);
	if($intIBlockItemsID>0) {
		$arFilter = array(
			'IBLOCK_ID' => $intIBlockItemsID,
		);
		if(strlen($strValueSearch)){
			$arFilterTmp = array(
				'LOGIC' => 'OR',
				'%NAME' => $strValueSearch,
				'%CODE' => $strValueSearch,
			);
			if(is_numeric($strValueSearch)){
				$arFilterTmp['ID'] = $strValueSearch;
			}
			$arFilter[] = $arFilterTmp;
		}
		$arNavParams = array(
			'iNumPage' => $intPage,
			'nPageSize' => $intResultsPerPage,
		);
		$resItems = \CIBlockElement::GetList(array(), $arFilter, false, $arNavParams, array('ID', 'NAME'));
		while($arItem = $resItems->GetNext()){
			$arJsonItems[] = array(
				'id' => IntVal($arItem['ID']),
				'text' => $arItem['~NAME'].' ['.$arItem['ID'].']',
			);
		}
		$intCount = IntVal($resItems->NavRecordCount);
	}
}
elseif($strCustomAction=='load_items_g'){
	$intIBlockItemsID = IntVal($arCurrentField['DATA']['LINK_IBLOCK_ID']);
	if($intIBlockItemsID>0) {
		$resSections = $obFilter->searchSectionsByText($intIBlockItemsID, $strValueSearch);
		while($arSection = $resSections->GetNext()){
			$intIndex++;
			if($intIndex >= $intIndexMin && $intIndex <= $intIndexMax){
				$arJsonItems[] = array(
					'id' => IntVal($arSection['ID']),
					'text' => $arSection['~NAME'].' ['.$arSection['ID'].']',
				);
			}
		}
		$intCount = $intIndex;
	}
}
// Load enums for PROPERTY_TYPE = S:directory
elseif($strCustomAction=='load_items_s_directory'){
	$strHlTableName = $arCurrentField['DATA']['USER_TYPE_SETTINGS']['TABLE_NAME'];
	if(strlen($strHlTableName) && \Bitrix\Main\Loader::includeModule('highloadblock')) {
		$arFilter = array();
		if(strlen($strValueSearch)){
			$arFilter[] = array(
				'LOGIC' => 'OR',
				'ID' => $strValueSearch,
				'%UF_NAME' => $strValueSearch,
				'%UF_XML_ID' => $strValueSearch,
			);
		}
		$arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array(
			'filter' => array('TABLE_NAME'=>$strHlTableName))
		)->fetch();
		$obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
		$strEntityDataClass = $obEntity->getDataClass();
		$resData = $strEntityDataClass::GetList(array(
			'filter' => $arFilter,
			'select' => array('ID','UF_NAME','UF_XML_ID'),
			'order' => array('ID' => 'ASC'),
			'limit' => $intResultsPerPage,
			'offset' => ($intPage-1)*$intResultsPerPage
		));
		while($arItem = $resData->fetch()) {
			$arJsonItems[] = array(
				'id' => $arItem['UF_XML_ID'],
				'text' => sprintf('%s [%d]', $arItem['UF_NAME'], $arItem['ID']),
			);
		}
		$intCount = IntVal($strEntityDataClass::getCount());
	}
}
// Load enums for PROPERTY_TYPE = N:_ID_LIST
elseif($strCustomAction=='load_items_n_id_list'){
	if($arParams['IBLOCK_ID']>0) {
		if($bSection) {
			$arFilter = array(
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			);
			if(strlen($strValueSearch)){
				$arFilterTmp = array(
					'LOGIC' => 'OR',
					['%NAME' => $strValueSearch],
					['%CODE' => $strValueSearch],
				);
				if(is_numeric($strValueSearch)){
					$arFilterTmp[] = ['ID' => $strValueSearch];
				}
				$arFilter[] = $arFilterTmp;
			}
			$arNavParams = array(
				'iNumPage' => $intPage,
				'nPageSize' => $intResultsPerPage,
			);
			$arSelect = ['ID', 'NAME'];
			$resItems = \Bitrix\IBlock\SectionTable::getList([
				'filter' => $arFilter,
				'select' => $arSelect,
			]);
			while($arItem = $resItems->fetch()){
				$arJsonItems[] = array(
					'id' => IntVal($arItem['ID']),
					'text' => $arItem['NAME'].' ['.$arItem['ID'].']',
				);
			}
			#
			$intCount = intVal($resItems->getSelectedRowsCount());
			$resCount = \Bitrix\IBlock\SectionTable::getList([
				'filter' => $arFilter,
				'select' => ['CNT'],
				'runtime' => array(
					new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'),
				)
			]);
			if($arCount = $resCount->fetch()){
				$intCount = intVal($arCount['CNT']);
			}
		}
		else{
			$arFilter = array(
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			);
			if(strlen($strValueSearch)){
				$arFilterTmp = array(
					'LOGIC' => 'OR',
					['%NAME' => $strValueSearch],
					['%CODE' => $strValueSearch],
				);
				if(is_numeric($strValueSearch)){
					$arFilterTmp[] = ['ID' => $strValueSearch];
				}
				$arFilter[] = $arFilterTmp;
			}
			$arNavParams = array(
				'iNumPage' => $intPage,
				'nPageSize' => $intResultsPerPage,
			);
			$arSelect = ['ID', 'NAME'];
			$resItems = \Bitrix\IBlock\ElementTable::getList([
				'filter' => $arFilter,
				'select' => $arSelect,
				'limit' => $intResultsPerPage,
				'offset' => ($intPage - 1) * $intResultsPerPage,
			]);
			while($arItem = $resItems->fetch()){
				$arJsonItems[] = array(
					'id' => intVal($arItem['ID']),
					'text' => $arItem['NAME'].' ['.$arItem['ID'].']',
				);
			}
			#
			$intCount = intVal($resItems->getSelectedRowsCount());
			$resCount = \Bitrix\IBlock\ElementTable::getList([
				'filter' => $arFilter,
				'select' => ['CNT'],
				'runtime' => array(
					new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'),
				)
			]);
			if($arCount = $resCount->fetch()){
				$intCount = intVal($arCount['CNT']);
			}
		}
	}
}
else {
	$bAjaxSelectItems = false;
}
if($bAjaxSelectItems){
	$bIncompleteResults = $intPage * $intResultsPerPage < $intCount;
	$arJsonItems = [
		'incomplete_results' => $bIncompleteResults,
		'items' => $arJsonItems,
		'total_count' => $intCount,
	];
	Helper::obRestart();
	print Json::encode($arJsonItems);
	die();
}

?>
<div class="wda-form">
	<?if(strlen($strType)):?>
		<?if(is_array($arCurrentLogic)):?>
			<input type="hidden" data-role="allow-save" />
			<div class="wda-field-select-wrapper" id="wda-field-select-wrapper" style="padding-top:0;">
				<?$bMultiple = in_array($strCurrentLogic, array('IN_LIST', 'NOT_IN_LIST')) 
					|| strpos($strCurrentLogic, 'M_') === 0;?>
				<?// TYPE = L ?>
				<?if($strType=='L' && $arCurrentField['IS_PROPERTY']):?>
					<?$arItems = $obFilter->getPropertyItems_L($arCurrentValue, $arCurrentField);?>
					<select name="value" class="wda-field-select-list" id="wda-field-select-l" 
						data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
						<?foreach($arItems as $strID => $strName):?>
							<option value="<?=$strID;?>" selected="selected"><?=$strName;?> [<?=$strID;?>]</option>
						<?endforeach?>
					</select>
					<?
					$arSelect2 = array(
						'SELECT_ID' => 'wda-field-select-l',
						'CUSTOM_ACTION' => 'load_items_l',
					);
					?>
				<?// TYPE = L ?>
				<?elseif($strType=='L' && $bSection):?>
					<?
					$arItems = IBlock::getPropertyEnums($arCurrentField['DATA']['FIELD_NAME'], $arParams['IBLOCK_ID']);
					foreach($arItems as $key => $arItem){
						if(!in_array($key, $arCurrentValue)){
							unset($arItems[$key]);
						}
					}
					?>
					<select name="value" class="wda-field-select-list" id="wda-field-select-l" 
						data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
						<?foreach($arItems as $strID => $arEnum):?>
							<option value="<?=$strID;?>" selected="selected"><?=$arEnum['VALUE'];?> [<?=$strID;?>]</option>
						<?endforeach?>
					</select>
					<?
					$arSelect2 = array(
						'SELECT_ID' => 'wda-field-select-l',
						'CUSTOM_ACTION' => 'load_items_l',
					);
					?>
				<?// TYPE = E ?>
				<?elseif($strType=='E' && $arCurrentField['IS_PROPERTY']):?>
					<?$arItems = $obFilter->getPropertyItems_E($arCurrentValue, $arCurrentField);?>
					<select name="value" class="wda-field-select-list" id="wda-field-select-e" 
						data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
						<?foreach($arItems as $strID => $strName):?>
							<option value="<?=$strID;?>" selected="selected"><?=$strName;?></option>
						<?endforeach?>
					</select>
					<?
					$arSelect2 = array(
						'SELECT_ID' => 'wda-field-select-e',
						'CUSTOM_ACTION' => 'load_items_e',
					);
					?>
				<?// TYPE = G ?>
				<?elseif($strType=='G' && $arCurrentField['IS_PROPERTY']):?>
					<?$arItems = $obFilter->getPropertyItems_G($arCurrentValue, $arCurrentField);?>
					<select name="value" class="wda-field-select-list" id="wda-field-select-g" 
						data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
						<?foreach($arItems as $strID => $strName):?>
							<option value="<?=$strID;?>" selected="selected"><?=$strName;?></option>
						<?endforeach?>
					</select>
					<?
					$arSelect2 = array(
						'SELECT_ID' => 'wda-field-select-g',
						'CUSTOM_ACTION' => 'load_items_g',
					);
					?>
				<?// TYPE = S:directory ?>
				<?elseif($strType=='S' && $arCurrentField['IS_PROPERTY'] && $strUserType=='directory'):?>
					<?$arItems = $obFilter->getPropertyItems_S_directory($arCurrentValue, $arCurrentField, ['ARRAY' => true]);?>
					<select name="value" class="wda-field-select-list" id="wda-field-select-s-directory" 
						data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
						<?foreach($arItems as $strID => $arItem):?>
							<option value="<?=$strID;?>" selected="selected"><?=$arItem['UF_NAME'];?> [<?=$arItem['ID'];?>]</option>
						<?endforeach?>
					</select>
					<?
					$arSelect2 = array(
						'SELECT_ID' => 'wda-field-select-s-directory',
						'CUSTOM_ACTION' => 'load_items_s_directory',
					);
					?>
				<?// TYPE = N:_ID_LIST ?>
				<?elseif($strType=='N' && $strUserType=='_ID_LIST'):?>
					<?
					if($bSection){
						$arItems = $obFilter->getPropertyItems_G($arCurrentValue, []);
					}
					else{
						$arItems = $obFilter->getPropertyItems_E($arCurrentValue, []);
					}
					?>
					<select name="value" class="wda-field-select-list" id="wda-field-select-n-id-list" data-just-id="Y"
						data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
						<?foreach($arItems as $strID => $strName):?>
							<option value="<?=$strID;?>" selected="selected"><?=$strName;?></option>
						<?endforeach?>
					</select>
					<?
					$arSelect2 = array(
						'SELECT_ID' => 'wda-field-select-n-id-list',
						'CUSTOM_ACTION' => 'load_items_n_id_list',
					);
					?>
				<?// TYPE = S:_List ?>
				<?elseif(in_array($strType, ['S', 'N']) && ($strUserType=='_List' || is_array($arCurrentField['LIST_VALUES']))):?>
					<select name="value" class="wda-field-select-list" id="wda-field-select-s-list"
						data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
						<?foreach($arCurrentField['LIST_VALUES'] as $strID => $strName):?>
							<option value="<?=$strID;?>" 
							<?if(is_array($arCurrentValue) && in_array($strID, $arCurrentValue)):?>selected="selected"<?endif?>><?
								print $strName;
							?></option>
						<?endforeach?>
					</select>
					<?
					$arSelect2 = array(
						'SELECT_ID' => 'wda-field-select-s-list',
						'AJAX' => false,
					);
					?>
				<?// TYPE = S, N ?>
				<?else:?>
					<?if($strType=='S' && $strUserType == '_TextType'):?>
						<?
						$strTextType = toLower($strCurrentValue);
						if(!in_array($strTextType, ['text', 'html'])){
							$strTextType = 'text';
						}
						?>
						<div id="wda-field-select-text-type">
							<input type="hidden" name="value" value="<?=$strTextType;?>" data-role="entity-select-value-hidden"/><br/>
							<label>
								<input type="radio" name="value" value="text" <?if($strTextType != 'html'):?>checked<?endif?> />
								<span>TEXT</span>
							</label>
							<label>
								<input type="radio" name="value" value="html" <?if($strTextType == 'html'):?>checked<?endif?> />
								<span>HTML</span>
							</label>
						</div>
						<script>
							$('#wda-field-select-text-type input[type=radio]').bind('change', function(e){
								$(this).closest('#wda-field-select-text-type')
									.find('input[data-role="entity-select-value-hidden"]').val($(this).val());
							});
						</script>
					<?elseif($strType=='S' && ($strUserType=='DateTime' || $strUserType=='Date')):?>
						<?if(in_array($strCurrentLogic, ['FOR_THE_LAST', 'NOT_FOR_THE_LAST'])):?>
							<?
							$strDatetimeType = 'd';
							if($arDatetimeValue = Filter::parseDatetimeValue($strCurrentValue, $strCurrentField, true)){
								$strCurrentValue = $arDatetimeValue[1];
								$strDatetimeType = $arDatetimeValue[2];
							}
							$arDatetimeFilterValues = Filter::getDatetimeFilterValues($strUserType=='DateTime');
							?>
							<div class="wda-select-wrapper">
								<input type="hidden" name="value" value="<?=$strCurrentValue;?>" data-role="entity-select-value-hidden"/><br/>
								<input type="text" value="<?=$strCurrentValue;?>" data-role="datetime-for-the-last"/>
								<select data-role="datetime-for-the-last">
									<?foreach($arDatetimeFilterValues as $strDatetimeKey => $strDatetimeValue):?>
										<option value="<?=$strDatetimeKey;?>"
											<?if($strDatetimeType == $strDatetimeKey):?> selected="selected"<?endif?>
											><?=$strDatetimeValue;?></option>
									<?endforeach?>
								</select>
							</div>
							<script>
								$('[data-role="datetime-for-the-last"]').change(function(e){
									var input = $('input[data-role="datetime-for-the-last"]'),
										value = input.val(),
										select = $('select[data-role="datetime-for-the-last"]'),
										type = select.val(),
										inputResult = $(this).closest('div').find('input[data-role="entity-select-value-hidden"]');
									value = parseInt(value);
									if(isNaN(value) || value<0){
										value = 0;
									}
									var text = value + ' ' + $('option:selected', select).text();
									inputResult.val(value + type).attr('data-text', text);
								}).keydown(function(e){
									if(e.keyCode==13) {
										$(this).trigger('change');
										$('#wda_conditions_save').trigger('click');
									}
								}).trigger('change').filter('input[type="text"]').focus();
							</script>
						<?else:?>
							<div id="wda-field-select-date">
								<?=\CAdminCalendar::CalendarDate('value', $strCurrentValue, 15, $strUserType=='DateTime'?true:false);?>
							</div>
						<?endif?>
						<script>
						$('#wda-field-select-date input[type=text]').attr('data-role', 'entity-select-value');
						</script>
					<?elseif($bMultiple):?>
						<?
						if(!is_array($arCurrentValue) || empty($arCurrentValue)){
							$arCurrentValue = array(
								'',
							);
						}
						?>
						<div>
							<table class="wda-field-select-text-multiple" data-role="entity-select-value-multiple">
								<tbody>
									<?foreach($arCurrentValue as $strValue):?>
										<tr>
											<td>
												<input type="text" name="value" class="wda-field-select-text" data-role="entity-select-value"
													value="<?=htmlspecialcharsbx($strValue);?>"
													placeholder="<?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_PLACEHOLDER_TEXT');?>"/>
											</td>
											<td>
												<a href="javascript:void(0)" title="Delete" data-role="entity-select-value-multiple-delete"
													class="wda-icon-delete">&times;</a>
											</td>
										</tr>
									<?endforeach?>
								</tbody>
							</table>
						</div>
						<div>
							<input type="button" data-role="entity-select-value-multiple-add"
								value="<?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_TEXT_ADD');?>" />
						</div>
					<?else:?>
						<input type="text" name="value" class="wda-field-select-text" data-role="entity-select-value"
							value="<?=htmlspecialcharsbx($strCurrentValue);?>"
							placeholder="<?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_PLACEHOLDER_TEXT');?>"/>
					<?endif?>
				<?endif?>
				<?if(is_array($arSelect2)):?>
					<script>
					wdaSelect2('#<?=$arSelect2['SELECT_ID'];?>', {
						<?if($arSelect2['AJAX'] !== false):?>
						ajax: {
							url: '<?=$GLOBALS['APPLICATION']->GetCurPageParam('custom_action='.$arSelect2['CUSTOM_ACTION'],array('custom_action'));?>',
							type: 'post',
							dataType: 'json',
							data: function (params) {
								var query = $.extend({}, <?=Json::encode($arPost);?>, {
									q: params.term,
									page: params.page
								});
								return query;
							},
							processResults: function(data, params) {
								params.page = params.page || 1;
								return {
									results: data.items,
									pagination: {
										more: (params.page * <?=$intResultsPerPage;?>) < data.total_count
									}
								};
							},
							cache: false
						},
						<?endif?>
						placeholder: '<?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_PLACEHOLDER_LIST');?>'
					}).bind('select2:select', function (e) {
						setTimeout(function(){
							$('.select2-search__field').focus();
						}, 10);
					}).next().find('.select2-search__field').each(function(){
						$(this).on('keydown', function(e){
							if(e.keyCode==27) {
								e.preventDefault();
							}
						});
					});
					<?/*
					$('#<?=$arSelect2['SELECT_ID'];?>').select2({
						ajax: {
							url: '<?=$GLOBALS['APPLICATION']->GetCurPageParam('custom_action='.$arSelect2['CUSTOM_ACTION'],array('custom_action'));?>',
							type: 'post',
							dataType: 'json',
							data: function (params) {
								var query = $.extend({}, <?=Json::encode($arPost);?>, {
									q: params.term,
									page: params.page
								});
								return query;
							},
							processResults: function(data, params) {
								params.page = params.page || 1;
								return {
									results: data.items,
									pagination: {
										more: (params.page * <?=$intResultsPerPage;?>) < data.total_count
									}
								};
							},
							cache: false
						},
						dropdownParent: $('#wda-field-select-wrapper').closest('.bx-core-adm-dialog-content'),
						dropdownPosition: 'below',
						placeholder: '<?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_PLACEHOLDER_LIST');?>',
						language: '<?=LANGUAGE_ID;?>'
					}).bind('select2:select', function (e) {
						setTimeout(function(){
							$('.select2-search__field').focus();
						}, 10);
					}).next().find('.select2-search__field').each(function(){
						$(this).on('keydown', function(e){
							if(e.keyCode==27) {
								e.preventDefault();
							}
						});
					});
					*/?>
					</script>
				<?endif?>
			</div>
			<input type="hidden" data-role="filter-title" value="" />
			<input type="hidden" data-role="filter-value" value="" />
		<?else:?>
			<div><?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_NO_LOGIC');?></div>
		<?endif?>
	<?else:?>
		<div><?=Helper::getMessage('WDA_FILTER_POPUP_FIELD_NO_FIELD');?></div>
	<?endif?>
</div>
