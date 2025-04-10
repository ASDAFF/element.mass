<?
class CWDA_ProductSetArticul extends CWDA_Plugin {
	CONST GROUP = 'CATALOG';
	CONST CODE = 'PRODUCT_SET_ARTICUL';
	CONST NAME = 'Создание наборов/комплектов по артикулам';
	//
	static function GetDescription() {
		$Descr = 'Плагин позволяет собирать наборы и комплекты для выбранных по фильтру товаров.<br/>Для этого у товара должно быть текстовое свойство с перечислением артикулов товаров из набора (напр., «ART1, ART2, ART3» - пробелы значения не имеют).<br/>В настройках указывается данное свойство, а также свойство, в котором указаны артикулы товаров.<br/>Если в результате обработки имеются ошибки, смотрите файл лога. О том, где он находится читайте <a href="http://www.webdebug.ru/marketplace/webdebug.antirutin/faq/#12538" target="_blank">на нашем сайте</a>.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'SET_TYPE' => 'Набор или комплект:',
			'SET_TYPE_2' => 'набор',
			'SET_TYPE_1' => 'комплект',
			//
			'SET_DELIMITER' => 'Разделитель:',
			'DELIMITER_COMMA' => 'запятая',
			'DELIMITER_SEMICOLON' => 'точка с запятой',
			'DELIMITER_OTHER' => 'другое (укажите отдельно)',
			//
			'PROP_LINK' => 'Свойство, содержащее артикулы через запятую:',
			'PROP_ARTICUL' => 'Свойство товара, содержащее артикул:',
			//
			'ALERT_PROPS_SAME' => 'Выбрано два раза одно и то же свойство.',
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
				$('#wdi_psa_delimiter').change(function(){
					if($(this).val()=='') {
						$('#wdi_psa_delimiter_other').show();
					} else {
						$('#wdi_psa_delimiter_other').hide();
					}
				});
			}
		});
		BX.addCustomEvent('onWdaBeforeSubmit', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				if($('#product_set_custom_prop_link').val()==$('#product_set_custom_prop_articul').val()) {
					alert('<?=self::GetMessage('ALERT_PROPS_SAME',true);?>');
					WdaCanSubmit = false;
				}
			}
		});
		//
		function WDA_<?=self::CODE?>_Fill(){
			var SelectLink = $('#product_set_custom_prop_link').html($('#wda_filter_param').find('optgroup[data-group=PROPERTIES]').html());
			SelectLink.find('option').not('[data-type=S]').remove();
			//
			var SelectArticul = $('#product_set_custom_prop_articul').html($('#wda_filter_param').find('optgroup[data-group=PROPERTIES]').html());
			SelectArticul.find('option').not('[data-type=S]').remove();
			//
			SelectLink.change();
			SelectArticul.change();
		}
		</script>
		<style>
		#wdi_psa_delimiter {width:220px;}
		#wdi_psa_delimiter_other {width:92px;}
		</style>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div id="wda_product_set_type">
				<?=self::GetMessage('SET_TYPE');?>
				<label><input type="radio" name="params[set_type]" value="2" checked="checked" /> <span><?=self::GetMessage('SET_TYPE_2');?></span></label>
				<label><input type="radio" name="params[set_type]" value="1" /> <span><?=self::GetMessage('SET_TYPE_1');?></span></label>
			</div>
			<br/>
			<div id="wda_product_set_delimiter">
				<div style="margin-bottom:2px;"><b><?=self::GetMessage('SET_DELIMITER');?></b></div>
				<div>
					<select name="params[delimiter]" id="wdi_psa_delimiter">
						<option value=","><?=self::GetMessage('DELIMITER_COMMA');?></option>
						<option value=";"><?=self::GetMessage('DELIMITER_SEMICOLON');?></option>
						<option value=""><?=self::GetMessage('DELIMITER_OTHER');?></option>
					</select>
					<input type="text" name="params[delimiter_other]" id="wdi_psa_delimiter_other" value="" style="display:none" />
				</div>
			</div>
			<br/>
			<div>
				<div style="margin-bottom:2px;"><b><?=self::GetMessage('PROP_LINK');?></b></div>
				<div>
					<select id="product_set_custom_prop_link" name="params[property_link]"></select>
				</div>
			</div>
			<br/>
			<div>
				<div style="margin-bottom:2px;"><b><?=self::GetMessage('PROP_ARTICUL');?></b></div>
				<div>
					<select id="product_set_custom_prop_articul" name="params[property_articul]"></select>
				</div>
			</div>
			<br/>
		</div>
		<?
	}
	static function GetOffersParams($IBlockID){
		if(isset($GLOBALS['WDA_PSA_OFFERS_IBLOCK_ID']) && isset($GLOBALS['WDA_PSA_OFFERS_PROPERTY_ID'])) {
			return;
		}
		if (!isset($GLOBALS['WDA_PSA_OFFERS_IBLOCK_ID']) && !isset($GLOBALS['WDA_PSA_OFFERS_PROPERTY_ID']) && CModule::IncludeModule('catalog')) {
			$arCatalog = CCatalog::GetByID($IBlockID);
			if ($arCatalog['OFFERS_IBLOCK_ID']>0 && $arCatalog['OFFERS_PROPERTY_ID']) {
				$intOffersIBlockID = $arCatalog['OFFERS_IBLOCK_ID'];
				$intOffersPropertyID = $arCatalog['OFFERS_PROPERTY_ID'];
			}
		}
		$GLOBALS['WDA_PSA_OFFERS_IBLOCK_ID'] = $intOffersIBlockID;
		$GLOBALS['WDA_PSA_OFFERS_PROPERTY_ID'] = $intOffersPropertyID;
	}
	static function GetProductFirstOffer($ElementID, $IBlockID){
		self::GetOffersParams($IBlockID);
		if(empty($GLOBALS['WDA_PSA_OFFERS_IBLOCK_ID']) || empty($GLOBALS['WDA_PSA_OFFERS_PROPERTY_ID'])) {
			return false;
		}
		$resOffersCount = CIBlockElement::GetList(array('SORT'=>'ASC','NAME'=>'ASC'),array('IBLOCK_ID'=>$GLOBALS['WDA_PSA_OFFERS_IBLOCK_ID'],'PROPERTY_'.$GLOBALS['WDA_PSA_OFFERS_PROPERTY_ID']=>$ElementID),false,false,array('ID'));
		return $resOffersCount->GetNext(false,false);
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		$strPropLink = $Params['property_link'];
		$strPropArticul = $Params['property_articul'];
		if(empty($strPropLink) || empty($strPropArticul)) {
			return false;
		}
		if(CModule::IncludeModule('catalog') && class_exists('CCatalogProductSet')) {
			$intType = $Params['set_type']==1 ? CCatalogProductSet::TYPE_SET : CCatalogProductSet::TYPE_GROUP;
			if (preg_match('#^PROPERTY_(\d+)$#i',$strPropLink,$M1)) {
				$arPropLink = false;
				$arPropArticul = false;
				foreach($arElement['PROPERTIES'] as $arProperty){
					if($arProperty['ID']==$M1[1]) {
						$arPropLink = $arProperty;
					}
				}
				$strDelimiter = $Params['delimiter'];
				if(!strlen($strDelimiter)) {
					$strDelimiter = $Params['delimiter_other'];
				}
				if(!strlen($strDelimiter)) {
					$strDelimiter = ',';
				}
				$arArticuls = is_array($arPropLink['VALUE']) ? $arPropLink['VALUE'] : explode(trim($strDelimiter),$arPropLink['VALUE']);
				foreach($arArticuls as $Key => $Value){
					if(!strlen($Value)) {
						unset($arArticuls[$Key]);
					} else {
						$arArticuls[$Key] = trim($Value);
					}
				}
				if(!empty($arArticuls)) {
					$arSetFields = array(
						'TYPE' => $intType,
						'SET_ID' => 0,
						'ITEM_ID' => $ElementID,
						'ITEMS' => array(),
					);
					$arSetItems = array();
					$intSort = 1;
					foreach($arArticuls as $strArticul){
						$arFilter = array(
							'IBLOCK_ID' => $arElement['IBLOCK_ID'],
							'='.$strPropArticul => $strArticul,
							'!ID' => $ElementID,
						);
						/*
						self::GetOffersParams($arElement['IBLOCK_ID']);
						if(!empty($GLOBALS['WDA_PSA_OFFERS_IBLOCK_ID'])) {
							$arFilter = array(
								'LOGIC' => 'OR',
								$arFilter,
								array_merge($arFilter,array('IBLOCK_ID'=>$GLOBALS['WDA_PSA_OFFERS_IBLOCK_ID'])),
							);
						}
						print_r($arFilter);
						die();
						*/
						$resItems = CIBlockElement::GetList(array(),$arFilter,false,false,array('ID','IBLOCK_ID'));
						while($arItem = $resItems->GetNext(false,false)){
							$arFirstOffer = self::GetProductFirstOffer($arItem['ID'],$arItem['IBLOCK_ID']);
							if($arFirstOffer!==false && is_array($arFirstOffer)) {
								$arItem['ID'] = $arFirstOffer['ID'];
							}
							$arSetItem = array(
								'ACTIVE' => 'Y',
								'ITEM_ID' => $arItem['ID'],
								'QUANTITY' => 1,
								'SORT' => $intSort++,
							);
							$arSetItems[] = $arSetItem;
						}
					}
					if(!empty($arSetItem)) {
						if($intType==CCatalogProductSet::TYPE_SET) {
							$fSum = 0;
							$strLastKey = end(array_keys($arSetItems));
							foreach($arSetItems as $Key => $arSetItem){
								$fDiscount = round(100/count($arSetItems),2);
								if($Key==$strLastKey) {
									$fDiscount = 100-$fSum;
								}
								$fSum += $fDiscount;
								$arSetItems[$Key]['DISCOUNT_PERCENT'] = $fDiscount;
							}
						}
						$arSetFields['ITEMS'] = $arSetItems;
						//
						$obCatalogProductSet = new CCatalogProductSet;
						$arProductSets = CCatalogProductSet::getAllSetsByProduct($ElementID, $intType);
						//
						if(is_array($arProductSets) && !empty($arProductSets)) {
							$arProductSets = array_shift($arProductSets);
							unset($arSetFields['SET_ID'], $arSetFields['ITEM_ID'], $arSetFields['TYPE']);
							if($obCatalogProductSet->Update($arProductSets['SET_ID'], $arSetFields)) {
								$obCatalogProductSet->RecalculateSetsByProduct($ElementID);
								$bResult = true;
							} else {
								CWDA::Log('Errors for element #'.$ElementID.' (update mode)');
								CWDA::Log($obCatalogProductSet->getErrors());
							}
						} else {
							$arSetFields['ACTIVE'] = 'Y';
							$obCatalogProductSet = new CCatalogProductSet;
							if ($obCatalogProductSet->Add($arSetFields)) {
								$bResult = true;
							} else {
								CWDA::Log('Errors for element #'.$ElementID.' (add mode)');
								CWDA::Log($obCatalogProductSet->getErrors());
							}
						}
					} else {
						CWDA::Log('No items found.');
					}
				} else {
					CWDA::Log('Empty articuls.');
				}
			}
		}
		return $bResult;
	}
}
?>