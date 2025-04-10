<?
class CWDA_FileUpload extends CWDA_Plugin {
	CONST GROUP = 'IMAGES';
	CONST CODE = 'FILE_UPLOAD';
	CONST NAME = 'Загрузка изображений, файлов';
	//
	static function GetDescription() {
		$Descr = 'Плагин для загрузки изображений/файлов в товары.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'PROP_GROUP_1' => 'Поле, свойство',
			'LOADING' => 'Загрузка..',
			//
			'ALERT_NO_SOURCE' => 'Укажите в каком поле/свойстве нужно удалить изображение или файл.',
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
		BX.addCustomEvent('onWdaAfterIBlockChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		BX.addCustomEvent('onWdaAfterActionChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		BX.addCustomEvent('onWdaBeforeSubmit', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				if ($('#wda_field_source option:selected').size()==0) {
					WdaCanSubmit = false;
					alert('<?=self::GetMessage('ALERT_NO_SOURCE',true);?>');
				}
				//
				var divPictures = $('#wda_pictures_<?=self::CODE?>').html(''),
					index = 0;
				$('#wda_settings_<?=self::CODE?> .adm-fileinput-item').each(function(){
					index++;
					var name = $('input[name="PICTURE[name]"]', this).val(),
						type = $('input[name="PICTURE[type]"]', this).val(),
						tmp_name = $('input[name="PICTURE[tmp_name]"]', this).val(),
						size = $('input[name="PICTURE[size]"]', this).val(),
						error = $('input[name="PICTURE[error]"]', this).val();
					divPictures
						.append('<input type="hidden" name="params[pictures]['+index+'][name]" value="'+name+'" />')
						.append('<input type="hidden" name="params[pictures]['+index+'][type]" value="'+type+'" />')
						.append('<input type="hidden" name="params[pictures]['+index+'][tmp_name]" value="'+tmp_name+'" />')
						.append('<input type="hidden" name="params[pictures]['+index+'][size]" value="'+size+'" />')
						.append('<input type="hidden" name="params[pictures]['+index+'][error]" value="'+error+'" />');
				});
			}
		});
		//
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			// Source
			var SelectSource = $('#wda_field_source').html(Select.html());
			SelectSource.find('option[value=""]').remove();
			SelectSource.find('optgroup').not('optgroup[data-group=FIELDS]').not('optgroup[data-group=PROPERTIES]').remove();
			SelectSource.find('optgroup option').not('[data-type=F]').remove();
			SelectSource.change();
			//
			SelectSource.on('change',function(){
				var SourceValue = $(this).val();
				$('#wda_additional_settings_<?=self::CODE?>').html('<div class="loading"><?=self::GetMessage('LOADING',true);?></div>');
				$.ajax({
					url: '<?=$GLOBALS['APPLICATION']->GetCurPageParam('show_additional_settings=Y&action='.self::CODE,array('show_action_settings','show_additional_settings','ACTION','IBLOCK_ID'));?>&iblock_id='+$('#wda_select_iblock').val()+'&source='+SourceValue,
					type: 'GET',
					data: '',
					success: function(HTML) {
						$('#wda_additional_settings_<?=self::CODE?>').html(HTML)
						BX.onCustomEvent(window, 'wda_field_callback', [this, $('#wda_additional_settings_<?=self::CODE?>')]); 
					}
				});
			});
		}
		//
		</script>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_1');?></div>
			<div>
				<div>
					<select name="params[field_source]" id="wda_field_source" class="wda_select_field"></select>
					<?=CWDA::ShowHint(self::GetMessage('SELECT_SOURCE_PRICE'));?>
				</div>
			</div>
			<br/>
			<div id="wda_additional_settings_<?=self::CODE?>"></div>
			<div id="wda_pictures_<?=self::CODE?>"></div>
			<script>setTimeout(function(){
				$('#wda_field_source').trigger('change');
			},250)</script>
			<br/>
		</div>
		<?
	}
	static function isFieldMultiple($strSource, $intIBlockID){
		if(preg_match('#^PROPERTY_(\d+)$#', $strSource, $arMatch)){
			$resProperty = \CIBlockProperty::getById($arMatch[1], $intIBlockID);
			if($arProperty = $resProperty->getNext()){
				if($arProperty['MULTIPLE'] == 'Y'){
					return true;
				}
			}
		}
		return false;
	}
	static function getFieldFileExtensions($strSource, $intIBlockID){
		if(preg_match('#^PROPERTY_(\d+)$#', $strSource, $arMatch)){
			$resProperty = \CIBlockProperty::getById($arMatch[1], $intIBlockID);
			if($arProperty = $resProperty->getNext()){
				if(strlen($arProperty['FILE_TYPE'])){
					return $arProperty['FILE_TYPE'];
				}
			}
		}
		return null;
	}
	static function ShowAdditionalSettings() {
		$strSource = htmlspecialcharsbx($_GET['source']);
		$intIBlockID = IntVal($_GET['iblock_id']);
		$strAllowUpload = 'F';
		if(in_array($strSource, array('PREVIEW_PICTURE', 'DETAIL_PICTURE'))){
			$strAllowUpload = 'I';
		}
		$strAllowUploadExt = self::getFieldFileExtensions($strSource, $intIBlockID);
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/admin_tools.php");
		ob_start();
		print \Bitrix\Main\UI\FileInput::createInstance(array(
				"name" => "PICTURE",
				"description" => false,
				"upload" => true,
				"allowUpload" => $strAllowUpload,
				"allowUploadExt" => $strAllowUploadExt,
				"medialib" => false,
				"fileDialog" => true,
				"cloud" => false,
				"delete" => true,
				"maxCount" => self::isFieldMultiple($strSource, $intIBlockID) ? null : 1,
		))->show();
		$strHtml = ob_get_clean();
		if(!CWDA::IsUtf()){
			$strHtml = CWDA::ConvertCharset($strHtml, 'CP1251', 'UTF-8');
		}
		print $strHtml;
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		#
		$strSource = $Params['field_source'];
		if(!strlen($strSource)){
			CWDA::Log('Empty source');
			return false;
		}
		#
		$strUploadRoot = defined('BX_TEMPORARY_FILES_DIRECTORY') ? BX_TEMPORARY_FILES_DIRECTORY : $_SERVER['DOCUMENT_ROOT'].'/upload/tmp';
		$strUploadRoot = str_replace('\\', '/', $strUploadRoot);
		$strUploadRoot = preg_replace('#/$#i', '', $strUploadRoot);
		if(!is_dir($strUploadRoot) || !is_readable($strUploadRoot)){
			CWDA::Log('Error get upload dir ('.$strUploadRoot.')');
			return false;
		}
		if(is_array($Params['pictures']) && !empty($Params['pictures'])){
			if(!self::isFieldMultiple($strSource, $arElement['IBLOCK_ID'])){
				$Params['pictures'] = array_slice($Params['pictures'], 0, 1);
			}
			foreach($Params['pictures'] as $intIndex => $arPicture){
				$Params['pictures'][$intIndex]['tmp_name'] = $strUploadRoot.$arPicture['tmp_name'];
			}
			#
			if(in_array($strSource, array('PREVIEW_PICTURE', 'DETAIL_PICTURE'))){
				$obElement = new CIBlockElement();
				$bResult = !!$obElement->update($ElementID, array(
					$strSource => reset($Params['pictures']),
				));
				if(!$bResult){
					CWDA::Log($obElement->LAST_ERROR);
				}
				unset($obElement);
			}
			elseif(preg_match('#^PROPERTY_(\d+)$#', $strSource, $arMatch)){
				CIBlockElement::setPropertyValuesEx($ElementID, $arElement['IBLOCK_ID'], array(
					$arMatch[1] => $Params['pictures'],
				));
				$bResult = true;
			}
		}
		return $bResult;
	}
}
?>