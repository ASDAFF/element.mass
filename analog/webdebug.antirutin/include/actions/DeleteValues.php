<?
class CWDA_DeleteValues extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'DELETE_VALUES';
	CONST NAME = 'Удаление значений полей и свойств';
	//
	static function GetDescription() {
		$Descr = 'Плагин позволяет очистить значения полей и свойств.<br/><b>Внимание!</b> Удаление значений обязательных полей (если обязательность отмечена в настройках инфоблока), например, символьный код, будут приводить к ошибкам (см. лог).';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'FIELD_TARGET' => 'Выберите поле/свойство',
			'FIELD_TARGET_HINT' => 'Выберите поле/свойство, значение которого будет очищено',
			//
			'QUANTITY_RESERVED' => 'Зарезервированное количество',
			'LENGTH' => 'Длина',
			'WIDTH' => 'Ширина',
			'HEIGHT' => 'Высота',
			//
			'ALERT_NO_FIELD_TARGET' => 'Выберите поле/свойство из списка!',
		);
		$MESS = trim($MESS[$Code]);
		if ($ConvertCharset && !CWDA::IsUtf()) {
			$MESS = CWDA::ConvertCharset($MESS);
		}
		return $MESS;
	}
	//
	static function AddHeadData() {
		?>
		<script>
		BX.addCustomEvent('onWdaAfterActionChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		BX.addCustomEvent('onWdaBeforeSubmit', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				 if ($.trim($('#wda_field_target').val())=='') {
					alert('<?=self::GetMessage('ALERT_NO_FIELD_TARGET',true);?>');
					WdaCanSubmit = false;
				}
			}
		});
		//
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			// Target
			var SelectTarget = $('#wda_field_target').html(Select.html());
			SelectTarget.find('option[value=ID]').remove();
			SelectTarget.find('option[value=NAME]').remove();
			SelectTarget.find('option[value=ACTIVE_DATE]').remove();
			SelectTarget.find('option[value=XML_ID]').remove();
			SelectTarget.find('option[value=SECTION_GLOBAL_ACTIVE]').remove();
			SelectTarget.find('option[value=PREVIEW_TEXT_TYPE]').remove();
			SelectTarget.find('option[value=DETAIL_TEXT_TYPE]').remove();
			SelectTarget.find('option[value=SHOW_COUNTER]').remove();
			SelectTarget.find('option[value=DATE_CREATE]').remove();
			SelectTarget.find('option[value=CREATED_BY]').remove();
			SelectTarget.find('option[value=TIMESTAMP_X]').remove();
			SelectTarget.find('option[value=MODIFIED_BY]').remove();
			SelectTarget.find('option[value=_HAS_OFFERS]').remove();
			SelectTarget.find('option[value=CATALOG_AVAILABLE]').remove();
			SelectTarget.find('option[value^=CATALOG_STORE_AMOUNT_]').remove();
			//
			<?$arItems = array('QUANTITY_RESERVED', 'LENGTH', 'WIDTH', 'HEIGHT');?>
			<?foreach($arItems as $strCode):?>
				SelectTarget.find('optgroup[data-group="CATALOG"]')
					.append('<option value="CATALOG_<?=$strCode;?>"><?=self::getMessage($strCode, true);?></option>');
			<?endforeach?>
			SelectTarget.change();
		}
		</script>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_settings_header"><?=self::GetMessage('FIELD_TARGET');?></div>
			<div>
				<div>
					<select name="params[field_target]" id="wda_field_target" class="wda_select_field"></select>
					<?=CWDA::ShowHint(self::GetMessage('FIELD_TARGET_HINT'));?>
				</div>
			</div>
			<br/>
		</div>
		<?
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		#
		$strTarget = $Params['field_target'];
		if(preg_match('#^PROPERTY_(\d+)$#i', $strTarget, $arMatch)){
			$intPropertyID = $arMatch[1];
			$arProperty = array();
			foreach($arElement['PROPERTIES'] as $arOneProperty){
				if($arOneProperty['ID'] == $intPropertyID){
					$arProperty = $arOneProperty;
					break;
				}
			}
			if(is_array($arProperty) && $arProperty['PROPERTY_TYPE']=='F'){
				$arValues = array();
				$arValueID = is_array($arProperty['PROPERTY_VALUE_ID']) ? $arProperty['PROPERTY_VALUE_ID'] : array($arProperty['PROPERTY_VALUE_ID']);
				$arValueID =  array_filter($arValueID);
				foreach($arValueID as $intValueID){
					$arValues[$intValueID] = array('VALUE' => array('del' => 'Y'));
				}
				CIBlockElement::setPropertyValueCode($ElementID, $intPropertyID, $arValues);
			}
			else {
				$arValue = array(
					$intPropertyID => false,
				);
				CIBlockElement::setPropertyValuesEx($ElementID, $arElement['IBLOCK_ID'], $arValue);
				static::resetFacetIndex($arElement['IBLOCK_ID'], $arElement['ID']);
			}
			$bResult = true;
		}
		elseif(preg_match('#^CATALOG_PRICE_(\d+)$#i', $strTarget, $arMatch)){
			$bResult = false;
			if(CModule::includeModule('catalog')){
				$bResult = true;
				$arPriceFilter = array(
					'PRODUCT_ID' => $ElementID,
					'CATALOG_GROUP_ID' => $arMatch[1],
				);
				$resPrices = CPrice::getList(array(), $arPriceFilter, false, false, array('ID'));
				while($arPrice = $resPrices->getNext(false, false)){
					if(!CPrice::delete($arPrice['ID'])){
						$bResult = false;
					}
				}
			}
		}
		elseif(preg_match('#^CATALOG_(.*?)$#i', $strTarget, $arMatch)){
			$bResult = false;
			if(CModule::includeModule('catalog')){
				$strKey = $arMatch[1];
				$bResult = CCatalogProduct::add(array(
					'ID' => $ElementID,
					$strKey => false,
				));
			}
		}
		else{
			switch($strTarget){
				case 'ACTIVE': 
					$mValue = 'N';
					break;
				case 'SORT': 
					$mValue = 500; 
					break;
				case 'PREVIEW_PICTURE':
				case 'DETAIL_PICTURE':
					$mValue = array('del' => 'Y');
					break;
				default:
					$mValue = false;
					break;
			}
			$arFields = array(
				$strTarget => $mValue,
			);
			$obElement = new CIBlockElement;
			$bResult = !!$obElement->update($ElementID, $arFields);
			if(!$bResult){
				CWDA::Log($obElement->LAST_ERROR);
			}
		}
		#
		return $bResult;
	}
}
?>