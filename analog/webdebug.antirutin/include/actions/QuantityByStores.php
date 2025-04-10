<?
class CWDA_QuantityByStores extends CWDA_Plugin {
	CONST GROUP = 'CATALOG';
	CONST CODE = 'QUANTITY_BY_STORES';
	CONST NAME = 'Заполнение общего остатка на основе остатков по складам';
	//
	static function GetDescription() {
		$Descr = 'Плагин копирует сумму остатков по выбранным складам в поле «Доступное количество».';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'STORES' => 'Выберите склады:',
			'ALERT_NO_STORES_SELECTED' => 'Выберите хотя бы один склад из списка.',
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
		BX.addCustomEvent('onWdaBeforeSubmit', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				if($('#wda_quantity_by_stores_select option:selected').length==0) {
					alert('<?=self::GetMessage('ALERT_NO_STORES_SELECTED',true);?>');
					WdaCanSubmit = false;
				}
			}
		});
		</script>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		$arStores = array();
		if (CModule::IncludeModule('catalog') && class_exists('CCatalogStore')) {
			$resStores = CCatalogStore::GetList(array('SORT'=>'ASC','TITLE'=>'ASC'));
			while ($arStore = $resStores->GetNext(false,false)) {
				if(!CWDA::IsUtf()){
					$arStore['TITLE'] = CWDA::ConvertCharset($arStore['TITLE'],'CP1251','UTF-8');
				}
				$arStores[$arStore['ID']] = $arStore['TITLE'];
			}
		}
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_additional_settings" id="wda_additional_settings_<?=self::CODE?>">
				<div class="wda_settings_header"><?=self::GetMessage('STORES');?></div>
				<select name="params[stores][]" id="wda_quantity_by_stores_select" multiple="multiple" size="6" style="min-width:200px">
					<?$bFirst=true;?>
					<?foreach($arStores as $intStore => $strStore):?>
						<option value="<?=$intStore;?>"<?if($bFirst):?> selected="selected"<?$bFirst=false;?><?endif?>><?=$strStore;?></option>
					<?endforeach?>
				</select>
			</div>
			<?/**/?>
		</div>
		<?
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		if(is_array($Params['stores']) && !empty($Params['stores'])) {
			if (CModule::IncludeModule('catalog')) {
				if(class_exists('CCatalogStore')) {
					$intQuantity = 0;
					$resStoresQuantity = CCatalogStoreProduct::GetList(array(''),array('PRODUCT_ID'=>$ElementID));
					while($arStoresQuantity = $resStoresQuantity->GetNext(false,false)){
						if(in_array($arStoresQuantity['STORE_ID'], $Params['stores'])) {
							$intQuantity += IntVal($arStoresQuantity['AMOUNT']);
						}
					}
					$arCatalogProduct = array(
						'ID' => $ElementID,
						'QUANTITY' => $intQuantity,
					);
					$bResult = CCatalogProduct::Add($arCatalogProduct);
					if(!$bResult) {
						CWDA::Log('Error at CCatalogProduct::Add [product id: '.$ElementID.', quantity: '.$intQuantity.']');
					}
				} else {
					CWDA::Log('Catalog has no stores support.');
				}
			} else {
				CWDA::Log('Catalog is not installed.');
			}
		} else {
			CWDA::Log('Stores are not selected.');
		}
		return $bResult;
	}
}
?>