<?
class CWDA_DeleteListValues extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'DELETE_LIST_VALUES';
	CONST NAME = 'Удаление значений из свойства типа "Список"';
	//
	static function GetDescription() {
		$Descr = 'Плагин позволяет из свойства типа "Список" удалить выбранные значения. Поддерживаются как множественные свойства, так и единичные.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'FIELD_TARGET' => 'Выберите свойство',
			'SELECT_ENUMS' => 'Выберите удаляемые значения:',
			//
			'ALERT_NO_FIELD_TARGET' => 'Выберите свойство из списка!',
			'ALERT_NO_ENUM_SELECTED' => 'Выберите удаляемые значения!',
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
				else if ($.trim($('#wda_list_delete_enums').val())=='') {
					alert('<?=self::GetMessage('ALERT_NO_ENUM_SELECTED',true);?>');
					WdaCanSubmit = false;
				}
			}
		});
		//
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			var IBlock = $('#wda_select_iblock').val();
			// Target
			var SelectTarget = $('#wda_field_target').html(Select.html()); // Property
			SelectTarget.find('optgroup').not('optgroup[data-group=PROPERTIES]').remove();
			SelectTarget.find('option').not('[data-type=L]').remove();
			SelectTarget.prepend('<option value="">---</option>');
			SelectTarget.val('');
			// Event handlers
			SelectTarget.on('change',function(){
				var TargetValue = $(this).val();
				$('#wda_additional_settings_<?=self::CODE?>').html('<div class="loading"><?=self::GetMessage('LOADING',true);?></div>');
				$.ajax({
					url: '<?=$GLOBALS['APPLICATION']->GetCurPageParam('show_additional_settings=Y&action='.self::CODE,array('show_action_settings','show_additional_settings','ACTION','IBLOCK_ID'));?>&iblock_id='+$('#wda_select_iblock').val()+'&target='+TargetValue,
					type: 'GET',
					data: '',
					success: function(HTML) {
						$('#wda_additional_settings_<?=self::CODE?>').html(HTML).find('input[type=checkbox]').each(function(){
							BX.adminFormTools.modifyCheckbox(this);
						});
						BX.onCustomEvent(window, 'wda_field_callback', [this, $('#wda_additional_settings_<?=self::CODE?>')]); 
					}
				});
			});
			//
			SelectTarget.change();
		}
		</script>
		<style>
		#wda_form #wda_additional_settings_SET_VALUES .loading {padding-left:34px; text-indent:0;}
		</style>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_settings_header"><?=self::GetMessage('FIELD_TARGET');?></div>
			<div>
				<div><select name="params[field_target]" id="wda_field_target" class="wda_select_field" data-callback="wda_field_callback"></select><?=CWDA::ShowHint(self::GetMessage('SELECT_PRICE_TARGET'));?></div>
			</div>
			<br/>
			<div class="wda_settings_header"><?=self::GetMessage('FIELD_VALUE');?></div>
			<div class="wda_additional_settings" id="wda_additional_settings_<?=self::CODE?>"><?=self::GetMessage('ALERT_NO_FIELD_TARGET_VALUE');?></div>
			<div id="wda_keep_exists_multiple_values_param_<?=self::CODE?>" style="display:none">
				<table>
					<tbody>
						<tr>
							<td class="label">
								<input type="checkbox" name="params[keep_exists_multiple_values]" value="Y" id="wda_keep_exists_multiple_values" />
							</td>
							<td class="value">
								<label for="wda_keep_exists_multiple_values">
									<?=self::GetMessage('OPTION_KEEP_EXISTS_MULTIPLE_VALUES');?>
									<?=CWDA::ShowHint(self::GetMessage('OPTION_KEEP_EXISTS_MULTIPLE_VALUES_HINT'));?>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?
	}
	static function ShowAdditionalSettings() {
		$IBlockID = IntVal($_GET['iblock_id']);
		$Target = htmlspecialcharsbx($_GET['target']);
		CModule::IncludeModule('iblock');
		$arEnums = array();
		if(preg_match('#^PROPERTY_(\d+)$#', $Target, $M)) {
			$intPropertyID = $M[1];
			$resEnums = CIBlockPropertyEnum::getList(array('SORT'=>'ASC','VALUE'=>'ASC'),array('IBLOCK_ID'=>$IBlockID,'PROPERTY_ID'=>$intPropertyID));
			while($arEnum = $resEnums->getNext(false,false)){
				$strTitle = $arEnum['VALUE'].' ['.$arEnum['XML_ID'].']';
				$arEnums[$arEnum['ID']] = $strTitle;
			}
			ob_start();
			?>
			<div class="wda_settings_header"><?=self::GetMessage('SELECT_ENUMS', true);?></div>
			<select name="ENUMS[]" multiple="multiple" size="8" id="wda_list_delete_enums" style="max-width:100%; min-width:300px;">
				<?foreach($arEnums as $intEnumID => $strEnum):?>
					<option value="<?=$intEnumID;?>"><?=$strEnum;?></option>
				<?endforeach?>
			</select>
			<?
			$strHTML = ob_get_clean();
			if(!CWDA::IsUtf()) {
				$strHTML = CWDA::ConvertCharset($strHTML, 'CP1251', 'UTF-8');
			}
			print $strHTML;
		}
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		#
		$strTarget = $Params['field_target'];
		$arDeleteEnumsID = is_array($Params['ENUMS']) && !empty($Params['ENUMS']) ? $Params['ENUMS'] : false;
		if(is_array($arElement['PROPERTIES']) && preg_match('#^PROPERTY_(\d+)$#', $strTarget, $arMatch) && $arDeleteEnumsID !== false) {
			$intPropertyID = $arMatch[1];
			foreach($arElement['PROPERTIES'] as $strPropCode => $arProp){
				if($arProp['ID'] == $intPropertyID){
					$arEnumsID = $arProp['VALUE_ENUM_ID'];
					$arEnumsID = is_array($arEnumsID) ? $arEnumsID : (is_numeric($arEnumsID) ? array($arEnumsID) : array());
					$arEnumsID = array_diff($arEnumsID, $arDeleteEnumsID);
					if($arProp['MULTIPLE']!='Y' && !empty($arEnumsID)){
						$arEnumsID = reset($arEnumsID);
					} elseif(empty($arEnumsID)){
						$arEnumsID = false;
					}
					CIBlockElement::SetPropertyValuesEx($ElementID, $arElement['IBLOCK_ID'], array(
						$intPropertyID => $arEnumsID,
					));
					static::resetFacetIndex($arElement['IBLOCK_ID'], $arElement['ID']);
					$bResult = true;
				}
			}
		}
		#
		return $bResult;
	}
}
?>