<?
class CWDA_CopyValues extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'COPY_VALUES';
	CONST NAME = 'Копирование значений (поля, свойства и др.)';
	CONST MODE_FIRST = 1;
	CONST MODE_IMPLODE = 2;
	//
	static function GetDescription() {
		$Descr = 'Плагин копирует значения одних свойств в другие. Поддерживаются следующие данные:<ul style="margin:0;padding:0 0 0 20px;"><li><b>поля элемента</b> (включая, помимо прочего, описание для анонса, детальное описание, картинку для анонса, детальную картинку, теги и др.),</li><li><b>свойства элемента</b> (включая множественные), в т.ч. свойства типа «Строка», «Число», «Список», «Файл», «Привязка к элементам», «Привязка к разделам», «Справочник», «HTML/текст», «Дата», «Дата/Время», «Деньги» и другие (на данный момент не поддерживается копирование в свойство типа «Видео», однако копирование из данного свойства доступно,</li><li><b>поля торгового каталога</b> (остаток, зарезервированное количество, вес, длина/ширина/высота, НДС, закупочная цена и другие),</li><li><b>цены</b>,</li><li><b>SEO-данные товара</b> (title, keywords, description, page_title).</li></ul>';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'FIELD_SOURCE' => 'Исходное поле/свойство',
			'FIELD_TARGET' => 'Конечное поле/свойство',
			'PROP_GROUP_ADDITIONAL_SETTINGS' => 'Дополнительные опции',
			'KEEP_VALUES' => 'Добавить к имеющимся значениям (только при копировании во множественное свойство)',
			'REMOVE_DUBLES' => 'Предотвратить дубликаты (только при копировании во множественное свойство)',
			'COPY_MULTIPLE_TEXT_MANY_TO_ONE' => 'Копирование множественного текста в немножественное:',
			'COPY_MULTIPLE_TEXT_MANY_TO_ONE__IMPLODE' => 'Перечислять через запятую',
			'COPY_MULTIPLE_TEXT_MANY_TO_ONE__FIRST' => 'Только первое значение',
			'COPY_MULTIPLE_TEXT_MANY_TO_ONE__LAST' => 'Только последнее значение',
			'COPY_CHECKBOX' => 'Копирование флагов (Y/N) (при копировании в текст): ',
			'COPY_CHECKBOX_CODE' => 'Y / N',
			'COPY_CHECKBOX_NAME' => 'Да / Нет',
			'COPY_CHECKBOX_Y' => 'Да',
			'COPY_CHECKBOX_N' => 'Нет',
			'COPY_MEASURE' => 'Копирование единицы измерения (при копировании в текст): ',
			'COPY_MEASURE_NAME' => 'Название (напр., «Штука»)',
			'COPY_MEASURE_SIGN' => 'Обозначение (напр., «шт»)',
			'COPY_MEASURE_INTL' => 'Обозначение международное (напр., «pc. 1»)',
			'COPY_MEASURE_CODE' => 'Код (напр., «PCE. NMB»)',
			'COPY_MEASURE_ID' => 'ID',
			'COPY_VAT' => 'Копирование НДС (при копировании в текст): ',
			'COPY_VAT_NAME' => 'Название (обычно «НДС 18%», «НДС 10%» и т.д.)',
			'COPY_VAT_RATE' => 'Процент (напр., «18%»)',
			'COPY_VAT_NUM' => 'Число (напр., 18)',
			'COPY_VAT_ID' => 'ID',
			//
			'ALERT_NO_FIELD_SOURCE' => 'Не выбрано свойство-источник',
			'ALERT_NO_FIELD_TARGET' => 'Не выбрано свойство-назначение',
			//
			'OPTION_TIMESTAMP_X' => 'Дата изменения',
			'OPTION_PREVIEW_PICTURE' => 'Картинка для анонса',
			'OPTION_DETAIL_PICTURE' => 'Детальная картинка',
			'OPTION_PURCHASING_PRICE' => 'Закупочная цена',
			'OPTION_QUANTITY_RESERVED' => 'Зарезервированное количество',
			'OPTION_QUANTITY_TRACE' => 'Количественный учет',
			'OPTION_CAN_BUY_ZERO' => 'Разрешить покупку при отсутствии товара (включая разрешение отрицательного количества товара)',
			'OPTION_SUBSCRIBE' => 'Разрешить подписку при отсутствии товара',
			'OPTION_VAT_INCLUDED' => 'НДС включен в цену',
			'OPTION_VAT_ID' => 'Величина НДС',
			'OPTION_WEIGHT' => 'Вес, г',
			'OPTION_WIDTH' => 'Ширина, мм',
			'OPTION_LENGTH' => 'Длина, мм',
			'OPTION_HEIGHT' => 'Высота, мм',
			'OPTION_MEASURE' => 'Единица измерения',
			'OPTION_MEASURE_RATIO' => 'Коэффициент единицы измерения',
			'OPTION_BARCODE' => 'Штрихкод (должен быть уникальным!)',
			//
			'CATALOG_STORE' => 'Остаток на складе «%s»',
			//
			'OPTGROUP_SEO' => 'SEO (Мета-теги)',
			'SEO_ELEMENT_META_TITLE' => '[TITLE] Заголовок окна браузера',
			'SEO_ELEMENT_META_KEYWORDS' => '[KEYWORDS] Ключевые слова',
			'SEO_ELEMENT_META_DESCRIPTION' => '[DESCRIPTION] Описание страницы',
			'SEO_ELEMENT_PAGE_TITLE' => '[PAGE_TITLE] Заголовок страницы',
		);
		$MESS = trim($MESS[$Code]);
		if ($ConvertCharset && !CWDA::IsUtf()) {
			$MESS = CWDA::ConvertCharset($MESS);
		}
		return $MESS;
	}
	//
	static function AddHeadData() {
		$arStores = CWDA::GetStoresList();
		?>
		<script>
		BX.addCustomEvent('onWdaAfterActionChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			var IBlock = $('#wda_select_iblock').val();
			// Source
			var SelectSource = $('#wda_field_source').html(Select.html());
			SelectSource.find('[value=ACTIVE_DATE],[value=SECTION_GLOBAL_ACTIVE],[value=CATALOG_AVAILABLE],[value^=CATALOG_STORE_AMOUNT_]').remove();
			if (IBlock>0) {
				SelectSource.append('<optgroup label="<?=self::GetMessage('OPTGROUP_SEO',true);?>" data-group="SEO"></label>');
			}
			SelectSource.change();
			// Add custom options
			var CustomOptions = [
				// Fieds
				{VALUE:'TIMESTAMP_X',NAME:'<?=self::GetMessage('OPTION_TIMESTAMP_X',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_TIMESTAMP_X',true);?>',TYPE:'S:DateTime',GROUP:'FIELDS'},
				{VALUE:'PREVIEW_PICTURE',NAME:'<?=self::GetMessage('OPTION_PREVIEW_PICTURE',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_PREVIEW_PICTURE',true);?>',TYPE:'F',GROUP:'FIELDS'},
				{VALUE:'DETAIL_PICTURE',NAME:'<?=self::GetMessage('OPTION_DETAIL_PICTURE',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_DETAIL_PICTURE',true);?>',TYPE:'F',GROUP:'FIELDS'},
				// Catalog
				{VALUE:'CATALOG_QUANTITY_RESERVED',NAME:'<?=self::GetMessage('OPTION_QUANTITY_RESERVED',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_QUANTITY_RESERVED',true);?>',TYPE:'N',GROUP:'CATALOG'},
				{VALUE:'CATALOG_QUANTITY_TRACE',NAME:'<?=self::GetMessage('OPTION_QUANTITY_TRACE',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_QUANTITY_TRACE',true);?>',TYPE:'L',GROUP:'CATALOG'},
				{VALUE:'CATALOG_CAN_BUY_ZERO',NAME:'<?=self::GetMessage('OPTION_CAN_BUY_ZERO',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_CAN_BUY_ZERO',true);?>',TYPE:'L',GROUP:'CATALOG'},
				{VALUE:'CATALOG_SUBSCRIBE',NAME:'<?=self::GetMessage('OPTION_SUBSCRIBE',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_SUBSCRIBE',true);?>',TYPE:'L',GROUP:'CATALOG'},
				{VALUE:'CATALOG_VAT_INCLUDED',NAME:'<?=self::GetMessage('OPTION_VAT_INCLUDED',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_VAT_INCLUDED',true);?>',TYPE:'C',GROUP:'CATALOG'},
				{VALUE:'CATALOG_VAT_ID',NAME:'<?=self::GetMessage('OPTION_VAT_ID',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_VAT_ID',true);?>',TYPE:'L',GROUP:'CATALOG'},
				{VALUE:'CATALOG_LENGTH',NAME:'<?=self::GetMessage('OPTION_LENGTH',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_LENGTH',true);?>',TYPE:'N:INT',GROUP:'CATALOG'},
				{VALUE:'CATALOG_WIDTH',NAME:'<?=self::GetMessage('OPTION_WIDTH',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_WIDTH',true);?>',TYPE:'N:INT',GROUP:'CATALOG'},
				{VALUE:'CATALOG_HEIGHT',NAME:'<?=self::GetMessage('OPTION_HEIGHT',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_HEIGHT',true);?>',TYPE:'N:INT',GROUP:'CATALOG'},
				{VALUE:'CATALOG_MEASURE',NAME:'<?=self::GetMessage('OPTION_MEASURE',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_MEASURE',true);?>',TYPE:'N:INT',GROUP:'CATALOG'},
				{VALUE:'CATALOG_MEASURE_RATIO',NAME:'<?=self::GetMessage('OPTION_MEASURE_RATIO',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_MEASURE_RATIO',true);?>',TYPE:'N',GROUP:'CATALOG'},
				{VALUE:'CATALOG_BARCODE',NAME:'<?=self::GetMessage('OPTION_BARCODE',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_BARCODE',true);?>',TYPE:'S',GROUP:'CATALOG'},
				// Stores
				<?foreach($arStores as $arStore):?>
				{VALUE:'<?=$arStore['WDA_CODE'];?>',NAME:'<?=$arStore['WDA_NAME'];?>',NAME_FULL:'<?=sprintf(self::GetMessage('CATALOG_STORE',true),$arStore['WDA_NAME_FULL']);?>',TYPE:'N',GROUP:'CATALOG'},
				<?endforeach?>
				// SEO
				{VALUE:'SEO_ELEMENT_META_TITLE',NAME:'<?=self::GetMessage('SEO_ELEMENT_META_TITLE',true);?>',NAME_FULL:'<?=self::GetMessage('SEO_ELEMENT_META_TITLE',true);?>',TYPE:'S',GROUP:'SEO'},
				{VALUE:'SEO_ELEMENT_META_KEYWORDS',NAME:'<?=self::GetMessage('SEO_ELEMENT_META_KEYWORDS',true);?>',NAME_FULL:'<?=self::GetMessage('SEO_ELEMENT_META_KEYWORDS',true);?>',TYPE:'S',GROUP:'SEO'},
				{VALUE:'SEO_ELEMENT_META_DESCRIPTION',NAME:'<?=self::GetMessage('SEO_ELEMENT_META_DESCRIPTION',true);?>',NAME_FULL:'<?=self::GetMessage('SEO_ELEMENT_META_DESCRIPTION',true);?>',TYPE:'S',GROUP:'SEO'},
				{VALUE:'SEO_ELEMENT_PAGE_TITLE',NAME:'<?=self::GetMessage('SEO_ELEMENT_PAGE_TITLE',true);?>',NAME_FULL:'<?=self::GetMessage('SEO_ELEMENT_PAGE_TITLE',true);?>',TYPE:'S',GROUP:'SEO'},
			];
			for(var i in CustomOptions) {
				if (!CustomOptions.hasOwnProperty(i)) continue;
				var Option = CustomOptions[i];
				SelectSource.find('optgroup[data-group='+Option.GROUP+']').append('<option value="'+Option.VALUE+'" data-name="'+Option.NAME+'" data-type="'+Option.TYPE+'" data-group="'+Option.GROUP+'">'+Option.NAME_FULL+'</option>')
			}
			// Target
			var SelectTarget = $('#wda_field_target').html(SelectSource.html());
			SelectTarget.find('option[value=TIMESTAMP_X]').remove();
			// Source (part2)
			//SelectSource.find('optgroup[data-group=SEO]').remove();
		}
		//
		BX.addCustomEvent('onWdaBeforeSubmit', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				if ($.trim($('#wda_field_source').val())=='') {
					alert('<?=self::GetMessage('ALERT_NO_FIELD_SOURCE',true);?>');
					WdaCanSubmit = false;
				} else if ($.trim($('#wda_field_target').val())=='') {
					alert('<?=self::GetMessage('ALERT_NO_FIELD_TARGET',true);?>');
					WdaCanSubmit = false;
				}
			}
		});
		</script>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<?/**/?>
			<div class="wda_settings_header"><?=self::GetMessage('FIELD_SOURCE');?></div>
			<div>
				<div><select name="params[field_source]" id="wda_field_source" class="wda_select_field" data-callback="wda_field_callback"></select></div>
			</div>
			<br/>
			<div class="wda_settings_header"><?=self::GetMessage('FIELD_TARGET');?></div>
				<div><select name="params[field_target]" id="wda_field_target" class="wda_select_field" data-callback="wda_field_callback"></select></div>
			</div>
			<br/>
			<?/**/?>
			<div class="wda_additional_settings" id="wda_additional_settings_<?=self::CODE?>">
				<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_ADDITIONAL_SETTINGS');?></div>
				<?/*
				<label>
					<input type="checkbox" name="params[keep_values]" id="wda_keep_values" value="Y" disabled />
					<?=self::GetMessage('KEEP_VALUES');?>
				</label>
				<br/><br/>
				<label>
					<input type="checkbox" name="params[remove_dubles]" id="wda_remove_dubles" value="Y" disabled />
					<?=self::GetMessage('REMOVE_DUBLES');?>
				</label>
				<br/><br/>
				*/?>
				<label>
					<?=self::GetMessage('COPY_MULTIPLE_TEXT_MANY_TO_ONE');?><br/>
					<select name="params[copy_multiple_text_many_to_one]" id="wda_copy_multiple_text_many_to_one" style="margin-top:3px">
						<option value="implode"><?=self::GetMessage('COPY_MULTIPLE_TEXT_MANY_TO_ONE__IMPLODE');?></option>
						<option value="first"><?=self::GetMessage('COPY_MULTIPLE_TEXT_MANY_TO_ONE__FIRST');?></option>
						<option value="last"><?=self::GetMessage('COPY_MULTIPLE_TEXT_MANY_TO_ONE__LAST');?></option>
					</select>
				</label>
				<br/><br/>
				<label>
					<?=self::GetMessage('COPY_CHECKBOX');?><br/>
					<select name="params[copy_checkbox]" id="wda_copy_checkbox" style="margin-top:3px">
						<option value="name"><?=self::GetMessage('COPY_CHECKBOX_NAME');?></option>
						<option value="code"><?=self::GetMessage('COPY_CHECKBOX_CODE');?></option>
					</select>
				</label>
				<br/><br/>
				<label>
					<?=self::GetMessage('COPY_MEASURE');?><br/>
					<select name="params[copy_measure]" id="wda_copy_measure" style="margin-top:3px">
						<option value="name"><?=self::GetMessage('COPY_MEASURE_NAME');?></option>
						<option value="sign"><?=self::GetMessage('COPY_MEASURE_SIGN');?></option>
						<option value="intl"><?=self::GetMessage('COPY_MEASURE_INTL');?></option>
						<option value="code"><?=self::GetMessage('COPY_MEASURE_CODE');?></option>
						<option value="id"><?=self::GetMessage('COPY_MEASURE_ID');?></option>
					</select>
				</label>
				<br/><br/>
				<label>
					<?=self::GetMessage('COPY_VAT');?><br/>
					<select name="params[copy_vat]" id="wda_copy_vat" style="margin-top:3px">
						<option value="name"><?=self::GetMessage('COPY_VAT_NAME');?></option>
						<option value="rate"><?=self::GetMessage('COPY_VAT_RATE');?></option>
						<option value="num"><?=self::GetMessage('COPY_VAT_NUM');?></option>
						<option value="id"><?=self::GetMessage('COPY_VAT_ID');?></option>
					</select>
				</label>
			</div>
			<?/**/?>
		</div>
		<?
	}
	static function ToSingle($arValue, $Key='VALUES', $Params=array() /*, $Mode=self::MODE_FIRST*/){
		if(!in_array($Key,array('VALUES','MULTIPLE','CHECKBOX_VALUE','TEXT_TYPE'))) {
			$Key = 'VALUES';
		}
		if(!isset($arValue[$Key])){
			$arValue[$Key] = $arValue['VALUES'];
		}
		$mResult = $arValue[$Key];
		if($arValue['MULTIPLE']=='Y') {
			if(is_array($arValue[$Key])) {
				switch($Params['copy_multiple_text_many_to_one']){
					case 'implode':
						$mResult = implode(', ',$arValue[$Key]);
						break;
					case 'first':
						foreach($arValue[$Key] as $mValue){
							$mResult = $mValue;
							break;
						}
						break;
					case 'last':
						foreach($arValue[$Key] as $mValue){
							$mResult = $mValue;
						}
						break;
				}
			}
		}
		return $mResult;
	}
	static function GetCheckboxValue($Value,$CanBeDefault=false){
		$Value = ToUpper($Value);
		if($CanBeDefault) {
			if($Value=='Y' || $Value==ToUpper(self::GetMessage('COPY_CHECKBOX_Y',true))) {
				return 'Y';
			} elseif($Value=='N' || $Value==ToUpper(self::GetMessage('COPY_CHECKBOX_N',true))) {
				return 'N';
			} else {
				return 'D';
			};
		}
		return ($Value=='Y' || $Value==ToUpper(self::GetMessage('COPY_CHECKBOX_Y',true))) ? 'Y' : 'N';
	}
	static function Map($mValue,$arValue,$arElement,$arProperty,$Callback){
		if(is_array($mValue)){
			foreach($mValue as $intIndex => $mItem){
				$mValue[$intIndex] = call_user_func_array($Callback,array($mItem,$intIndex,$arValue,$arElement,$arProperty));
			}
		} else {
			$mValue = call_user_func_array($Callback,array($mValue,false,$arValue,$arElement,$arProperty));
		}
		return $mValue;
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = true;
		$Source = $Params['field_source'];
		$Target = $Params['field_target'];
		//
		// find source value(s)
		$arValue = array(
			'TYPE' => false,
			'USER_TYPE' => false,
			'MULTIPLE' => 'N',
			'VALUES' => null,
		);
		if (preg_match('#^PROPERTY_(\d+)$#',$Source,$M)) { // Property
			$strID = $M[1];
			foreach($arElement['PROPERTIES'] as $intPropertyID => $arProperty){
				if($arProperty['ID']==$strID) {
					$arValue['TYPE'] = $arProperty['PROPERTY_TYPE'];
					$arValue['USER_TYPE'] = $arProperty['USER_TYPE'];
					$arValue['MULTIPLE'] = $arProperty['MULTIPLE'];
					// Default value
					$arValue['VALUES'] = $arProperty['VALUE'];
					// Detetmine value
					switch($arProperty['PROPERTY_TYPE']){
						case 'S':
							switch(ToUpper($arProperty['USER_TYPE'])){
								case 'HTML':
									if(is_array($arProperty['VALUE']) && isset($arProperty['VALUE']['TEXT']) && isset($arProperty['VALUE']['TYPE'])) {
										if($arProperty['VALUE']['TYPE']=='HTML') {
											$arValue['VALUES'] = $arProperty['~VALUE']['TEXT'];
										} else {
											$arValue['VALUES'] = $arProperty['VALUE']['TEXT'];
										}
										$arValue['TEXT_TYPE'] = $arProperty['VALUE']['TYPE'];
									}
									break;
								case 'VIDEO':
									if(is_array($arValue['VALUES'])) {
										if(isset($arValue['VALUES']['path'])) {
											$arValue['VALUES'] = $arValue['VALUES']['path'];
										} else {
											$arValue['VALUES'] = '';
										}
									}
									break;
								case 'DATE':
									// custom prepare saved value
									break;
								case 'DATETIME':
									// custom prepare saved value
									break;
								case 'MONEY':
									// custom prepare saved value
									break;
								case 'MAP_YANDEX':
									// custom prepare saved value
									break;
								case 'MAP_GOOGLE':
									// custom prepare saved value
									break;
								case 'TOPICID':
									// custom prepare saved value
									break;
								case 'FILEMAN':
									// custom prepare saved value
								case 'ELEMENTXMLID':
									if(!empty($arProperty['VALUE'])) {
										$arValue['VALUES'] = array();
										$arValue['ELEMENT_ID'] = array();
										$arValue['ELEMENT_IBLOCK_ID'] = array();
										$arValue['ELEMENT_XML_ID'] = array();
										$resItems = CIBlockElement::GetList(array(),array('=XML_ID'=>$arProperty['VALUE']),false,false,array('ID','NAME','IBLOCK_ID','XML_ID'));
										while($arItem = $resItems->GetNext()){
											$arValue['VALUES'][] = $arItem['~NAME'];
											$arValue['ELEMENT_ID'][] = $arItem['ID'];
											$arValue['ELEMENT_IBLOCK_ID'][] = $arItem['IBLOCK_ID'];
											$arValue['ELEMENT_XML_ID'][] = $arItem['XML_ID'];
										}
									}
									break;
								case 'DIRECTORY':
									if(!empty($arProperty['VALUE']) && strlen($arProperty['USER_TYPE_SETTINGS']['TABLE_NAME']) && CModule::IncludeModule('highloadblock')) {
										$arValue['VALUES'] = array();
										$arValue['DIRECTORY_XML_ID'] = array();
										$arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array("filter" => array('TABLE_NAME'=>$arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
										$obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
										$strEntityDataClass = $obEntity->getDataClass();
										//
										$resData = $strEntityDataClass::GetList(array(
											'filter' => array('UF_XML_ID'=>$arProperty['VALUE']),
											'select' => array('UF_XML_ID','UF_NAME'),
											'limit' => '100',
										));
										while($arItem = $resData->Fetch()) {
											$arValue['VALUES'][] = $arItem['UF_NAME'];
											$arValue['DIRECTORY_XML_ID'][] = $arItem['UF_XML_ID'];
										}
									}
									break;
								default:
									$arValue['VALUES'] = $arProperty['~VALUE'];
									break;
							}
							break;
						case 'N':
							if(is_array($arValue['VALUES'])) {
								foreach($arValue['VALUES'] as $Key => $Value){
									$arValue['VALUES'][$Key] = FloatVal($Value);
								}
							} else {
								$arValue['VALUES'] = FloatVal($arValue['VALUES']);
							}
							break;
						case 'L':
							if(!empty($arProperty['VALUE'])) {
								$arValue['VALUES'] = array();
								$arValue['LIST_ID'] = array();
								$resEnums = CIBlockPropertyEnum::GetList(array(),array('PROPERTY_ID'=>$arProperty['ID'],'VALUE'=>$arProperty['VALUE']));
								while($arEnum = $resEnums->GetNext()){
									$arValue['VALUES'][] = $arEnum['~VALUE'];
									$arValue['LIST_ID'][] = $arEnum['ID'];
								}
							}
							break;
						case 'F':
							if(is_array($arValue['VALUES'])) {
								foreach($arValue['VALUES'] as $Key => $Value){
									if(is_numeric($Value)) {
										$arValue['VALUES'][$Key] = CFile::GetPath($Value);
									} else {
										unset($arValue['VALUES'][$Key]);
									}
								}
							} elseif(is_numeric($arValue['VALUES'])) {
								$arValue['VALUES'] = CFile::GetPath($arValue['VALUES']);
							}
							break;
						case 'E':
							if(!empty($arProperty['VALUE']) && !empty($arProperty['LINK_IBLOCK_ID'])) {
								$arValue['VALUES'] = array();
								$arValue['ELEMENT_ID'] = array();
								$arValue['ELEMENT_IBLOCK_ID'] = array();
								$arValue['ELEMENT_XML_ID'] = array();
								$resItems = CIBlockElement::GetList(array(),array('IBLOCK_ID'=>$arProperty['LINK_IBLOCK_ID'],'ID'=>$arProperty['VALUE']),false,false,array('ID','NAME','IBLOCK_ID','XML_ID'));
								while($arItem = $resItems->GetNext()){
									$arValue['VALUES'][] = $arItem['~NAME'];
									$arValue['ELEMENT_ID'][] = $arItem['ID'];
									$arValue['ELEMENT_IBLOCK_ID'][] = $arItem['IBLOCK_ID'];
									$arValue['ELEMENT_XML_ID'][] = $arItem['XML_ID'];
								}
							}
							break;
						case 'G':
							if(!empty($arProperty['VALUE']) && !empty($arProperty['LINK_IBLOCK_ID'])) {
								$arValue['VALUES'] = array();
								$arValue['SECTION_ID'] = array();
								$resSections = CIBlockSection::GetList(array(),array('IBLOCK_ID'=>$arProperty['LINK_IBLOCK_ID'],'ID'=>$arProperty['VALUE']),false,array('ID','NAME'));
								while($arSection = $resSections->GetNext()){
									$arValue['VALUES'][] = $arSection['~NAME'];
									$arValue['SECTION_ID'][] = $arSection['ID'];
								}
							}
							break;
					}
					//
				}
			}
		} elseif(in_array($Source,array('ID','NAME','CODE','XML_ID','SORT','PREVIEW_TEXT_TYPE','DETAIL_TEXT_TYPE','SHOW_COUNTER','TAGS'))){ // Simple string value
			$arValue['VALUES'] = $arElement['~'.$Source];
			$arValue['TYPE'] = 'S';
		} elseif($Source=='ACTIVE'){
			$arValue['TYPE'] = 'S';
			$arValue['CHECKBOX_VALUE'] = $arElement[$Source]=='Y'?'Y':'N';
			$arValue['VALUES'] = $Params['copy_checkbox']=='name' ? self::GetMessage('COPY_CHECKBOX_'.$arValue['CHECKBOX_VALUE'],true) : $arValue['CHECKBOX_VALUE'];
		} elseif(in_array($Source,array('PREVIEW_TEXT','DETAIL_TEXT'))){ // Preview text || detail text
			$arValue['VALUES'] = $arElement['~'.$Source];
			$arValue['TYPE'] = 'S';
			$arValue['USER_TYPE'] = 'HTML';
			$arValue['TEXT_TYPE'] = $arElement[$Source.'_TYPE'];
		} elseif(in_array($Source,array('PREVIEW_PICTURE','DETAIL_PICTURE'))){ // Preview picture || detail picture
			$arValue['VALUES'] = is_numeric($arElement[$Source]) ? CFile::GetPath($arElement[$Source]) : '';
			$arValue['TYPE'] = 'F';
		} elseif(in_array($Source,array('DATE_ACTIVE_FROM','DATE_ACTIVE_TO','DATE_CREATE','TIMESTAMP_X'))){ // Date
			$arValue['VALUES'] = $arElement[$Source];
			$arValue['TYPE'] = 'S';
			$arValue['USER_TYPE'] = 'DateTime';
		} elseif(preg_match('#^CATALOG_PRICE_(\d+)$#',$Source,$M)) {
			$intPriceID = $M[1];
			$arValue['VALUES'] = FloatVal($arElement[$Source]);
			$arValue['TYPE'] = 'N';
		} elseif(preg_match('#^CATALOG_STORE_(\d+)$#',$Source,$M)) {
			$arValue['VALUES'] = 0;
			$arValue['TYPE'] = 'N';
			$resStoreItem = CCatalogStoreProduct::GetList(array(),array('STORE_ID'=>$M[1],'PRODUCT_ID'=>$ElementID),false,false,array('AMOUNT'));
			if ($arStoreItem = $resStoreItem->GetNext(false,false)) {
				$arValue['VALUES'] = IntVal($arStoreItem['AMOUNT']);
			}
		} elseif(preg_match('#^CATALOG_([A-z0-9-_]+)$#',$Source,$M)) {
			$strCode = $M[1];
			switch($strCode){
				case 'QUANTITY':
				case 'QUANTITY_RESERVED':
				case 'PURCHASING_PRICE':
				case 'WEIGHT':
				case 'WIDTH':
				case 'HEIGHT':
				case 'LENGTH':
					$arValue['TYPE'] = 'N';
					$arValue['VALUES'] = FloatVal($arElement[$Source]);
					break;
				case 'QUANTITY_TRACE':
				case 'CAN_BUY_ZERO':
				case 'SUBSCRIBE':
				case 'VAT_INCLUDED':
					$arValue['TYPE'] = 'S';
					$arValue['CHECKBOX_VALUE'] = $arElement[$Source]=='Y'?'Y':'N';
					$arValue['VALUES'] = $Params['copy_checkbox']=='name' ? self::GetMessage('COPY_CHECKBOX_'.$arValue['CHECKBOX_VALUE'],true) : $arValue['CHECKBOX_VALUE'];
					break;
				case 'VAT_ID':
					if($arElement[$Source]>0) {
						$arVatList = CWDA::GetVatList(false);
						if(is_array($arVatList) && is_array($arVatList[$arElement[$Source]])) {
							$arVatItem = $arVatList[$arElement[$Source]];
							switch($Params['copy_vat']){
								case 'rate':
									$arValue['TYPE'] = 'S';
									$arValue['VALUES'] = FloatVal($arVatItem['RATE']).'%';
									break;
								case 'num':
									$arValue['TYPE'] = 'N';
									$arValue['VALUES'] = FloatVal($arVatItem['RATE']);
									break;
								case 'id':
									$arValue['TYPE'] = 'N';
									$arValue['VALUES'] = IntVal($arVatItem['ID']);
									break;
								default:
									$arValue['TYPE'] = 'S';
									$arValue['VALUES'] = $arVatItem['NAME'];
									break;
							}
						}
					}
					break;
				case 'MEASURE':
					if($arElement[$Source]>0) {
						$arMeasureList = CWDA::GetMeasureList();
						if(is_array($arMeasureList) && is_array($arMeasureList[$arElement[$Source]])) {
							$arMeasureItem = $arMeasureList[$arElement[$Source]];
							switch($Params['copy_measure']){
								case 'sign':
									$arValue['TYPE'] = 'S';
									$arValue['VALUES'] = $arMeasureItem['SYMBOL_RUS'];
									break;
								case 'intl':
									$arValue['TYPE'] = 'S';
									$arValue['VALUES'] = $arMeasureItem['SYMBOL_INTL'];
									break;
								case 'code':
									$arValue['TYPE'] = 'S';
									$arValue['VALUES'] = $arMeasureItem['SYMBOL_LETTER_INTL'];
									break;
								case 'id':
									$arValue['TYPE'] = 'N';
									$arValue['VALUES'] = IntVal($arMeasureItem['ID']);
									break;
								default:
									$arValue['TYPE'] = 'S';
									$arValue['VALUES'] = $arMeasureItem['MEASURE_TITLE'];
									break;
							}
						}
					}
					break;
				case 'MEASURE_RATIO':
					$arValue['TYPE'] = 'N';
					$arValue['VALUES'] = CWDA::GetMeasureRatio($arElement['ID']);
					break;
				case 'BARCODE':
					$arValue['TYPE'] = 'S';
					$arValue['MULTIPLE'] = 'Y';
					$arValue['VALUES'] = array();
					$resBarcode = CCatalogStoreBarCode::GetList(array(),array('PRODUCT_ID'=>$arElement['ID']));
					while($arBarcode = $resBarcode->GetNext(false,false)) {
						$arValue['VALUES'][] = $arBarcode['BARCODE'];
					}
					break;
			}
		} elseif (preg_match('#^SEO_([\w\d_]+)$#',$Source,$M)) {
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arElement['IBLOCK_ID'], $arElement['ID']);
			$arValue['VALUES'] = html_entity_decode($ipropValues->GetValue($M[1]));
		}
		// Save value
		$IBlockElement = new CIBlockElement;
		if (preg_match('#^PROPERTY_(\d+)$#',$Target,$M)) { // Property
			foreach($arElement['PROPERTIES'] as $arProperty){
				if($arProperty['ID']==$M[1]) {
					$arPropValue = $arValue['VALUES'];
					if($arProperty['MULTIPLE']!='Y') {
						$arPropValue = self::ToSingle($arValue,false,$Params);
					}
					// Prepare for save
					$arPropValue = self::Map($arPropValue,$arValue,$arElement,$arProperty,function($mValue,$intIndex,$arValue,$arElement,$arProperty){
						$mResult = $mValue;
						#$strType = $arProperty['PROPERTY_TYPE'].(!empty($arProperty['USER_TYPE'])?':'.$arProperty['USER_TYPE']:'');
						switch($arProperty['PROPERTY_TYPE']){
							case 'S':
								switch(ToUpper($arProperty['USER_TYPE'])){
									case 'HTML':
										$strTextType = 'text';
										if(in_array(ToUpper($arValue['TEXT_TYPE']),array('TEXT','HTML'))) {
											$strTextType = $arValue['TEXT_TYPE'];
										} elseif(in_array(ToUpper($arProperty['DEFAULT_VALUE']['TYPE']),array('TEXT','HTML'))) {
											$strTextType = ToLower($arProperty['DEFAULT_VALUE']['TYPE']);
										}
										$mResult = array(
											'VALUE' => array(
												'TEXT' => $mValue,
												'TYPE' => $strTextType,
											),
											'DESCRIPTION' => '',
										);
										break;
									case 'VIDEO':
										/*
										if(strlen($mValue) && is_file($_SERVER['DOCUMENT_ROOT'].$mValue)) {
											$mResult = array(
												'VALUE' => array(
													'PATH' => $mValue,
													'WIDTH' => '400',
													'HEIGHT' => '300',
													'TITLE' => '',
													'DURATION' => '',
													'AUTHOR' => '',
													'DATE' => '',
													'DESC' => '',
													'FILE' => array (
														'name' => '',
														'type' => '',
														'tmp_name' => '',
														'error' => 4,
														'size' => 0,
													)
												),
											);
										} else {
											$mResult = NULL;
											CWDA::Log('File not exists: '.$mValue);
										}
										*/
										break;
									case 'DATE':
										if(strlen($mValue) && CheckDateTime($mValue,FORMAT_DATE)){
											$mResult = $mValue;
										} else {
											$mResult = NULL;
											CWDA::Log('Wrong date format: '.$mValue);
										}
										break;
									case 'DATETIME':
										if(strlen($mValue) && CheckDateTime($mValue,FORMAT_DATETIME)){
											$mResult = $mValue;
										} else {
											$mResult = NULL;
											CWDA::Log('Wrong datetime format: '.$mValue);
										}
										break;
									case 'MONEY':
										//
										break;
									case 'MAP_YANDEX':
										//
										break;
									case 'MAP_GOOGLE':
										//
										break;
									case 'MAP_GOOGLE':
										//
										break;
									case 'TOPICID':
										//
										break;
									case 'FILEMAN':
										//
										break;
									case 'ELEMENTXMLID':
										if(is_array($arValue['ELEMENT_XML_ID'])) { // E => S:ElementXmlID, not create if exists!!!
											$mResult = $arValue['ELEMENT_XML_ID'][$intIndex];
										} else { // * => E
											if(!empty($mValue)) {
												$resItem = CIBlockElement::GetList(array(),array('=XML_ID'=>$mValue),false,false,array('ID','NAME','XML_ID'));
												if($arItem = $resItem->GetNext(false,false)){
													$mResult = $arItem['XML_ID'];
												}
											}
										}
										break;
									case 'DIRECTORY':
										if(is_array($arValue['DIRECTORY_XML_ID'])) { // S:directory => S:directory
											$mResult = $arValue['DIRECTORY_XML_ID'][$intIndex];
										} else { // * => S:directory
											if(!empty($mValue) && strlen($arProperty['USER_TYPE_SETTINGS']['TABLE_NAME']) && CModule::IncludeModule('highloadblock')) {
												$arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array("filter" => array('TABLE_NAME'=>$arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
												$obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
												$strEntityDataClass = $obEntity->getDataClass();
												//
												$resData = $strEntityDataClass::GetList(array(
													'filter' => array('UF_NAME' => $mValue),
													'select' => array('UF_XML_ID'),
													'limit' => '100',
												));
												if($arItem = $resData->Fetch()) {
													$mResult = $arItem['UF_XML_ID'];
												} else {
													$strXmlID = CUtil::Translit($mValue, 'ru', array(
														'max_len' => 100,
														'change_case' => 'L',
														'replace_space' => '_',
														'replace_other' => '_',
														'delete_repeat_replace' => true,
													));
													$arFields = array(
														'UF_NAME' => $mValue,
														'UF_XML_ID' => $strXmlID,
													);
													if ($strEntityDataClass::Add($arFields)) {
														$mResult = $strXmlID;
													}
												}
											}
										}
										break;
								}
								break;
							case 'N':
								//
								break;
							case 'L':
								if(is_array($arValue['LIST_ID'])) { // L => L
									$mResult = $arValue['LIST_ID'][$intIndex];
								} else { // * => L
									if(!empty($mValue)) {
										$arValue['VALUES'] = array();
										$arValue['LIST_ID'] = array();
										$resEnums = CIBlockPropertyEnum::GetList(array(),array('IBLOCK_ID'=>$arElement['IBLOCK_ID'],'PROPERTY_ID'=>$arProperty['ID'],'VALUE'=>$mValue));
										if($arEnum = $resEnums->GetNext(false,false)){
											$mResult = $arEnum['ID'];
										} else {
											$arFields = array(
												'PROPERTY_ID' => $arProperty['ID'],
												'VALUE' => $mValue,
											);
											$IBlockPropertyEnum = new CIBlockPropertyEnum;
											if ($ID = $IBlockPropertyEnum->Add($arFields)) {
												$mResult = $ID;
											} else {
												$mResult = NULL;
												CWDA::Log('Add enum error: '.$IBlockPropertyEnum->LAST_ERROR.', fields: '.print_r($arFields,1));
											}
											unset($IBlockPropertyEnum);
										}
									}
								}
								break;
							case 'F':
								if(strlen($mValue) && is_file($_SERVER['DOCUMENT_ROOT'].$mValue)) {
									$mResult = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$mValue);
								} else {
									$mResult = NULL;
									CWDA::Log('File not exists: '.$mValue);
								}
								break;
							case 'E':
								if(is_array($arValue['ELEMENT_ID']) && $arValue['ELEMENT_IBLOCK_ID'][$intIndex]==$arProperty['LINK_IBLOCK_ID']) { // E => E
									$mResult = $arValue['ELEMENT_ID'][$intIndex];
								} else { // * => E
									if(!empty($mValue) && !empty($arProperty['LINK_IBLOCK_ID'])) {
										$arValue['VALUES'] = array();
										$arValue['ELEMENT_ID'] = array();
										$resItem = CIBlockElement::GetList(array(),array('IBLOCK_ID'=>$arProperty['LINK_IBLOCK_ID'],'=NAME'=>$mValue),false,false,array('ID','NAME'));
										if($arItem = $resItem->GetNext(false)){
											$mResult = $arItem['ID'];
										} else {
											$strCode = CUtil::Translit($mValue, 'ru', array(
												'max_len' => 100,
												'change_case' => 'L',
												'replace_space' => '_',
												'replace_other' => '_',
												'delete_repeat_replace' => true,
											));
											$arFields = array(
												'IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'],
												'NAME' => $mValue,
												'CODE' => $strCode,
											);
											if($arValue['ELEMENT_ID'][$intIndex]>0){
												$arFields['EXTERNAL_ID'] = $arValue['ELEMENT_ID'][$intIndex];
											}
											$IBlockElement = new CIBlockElement;
											if ($ID = $IBlockElement->Add($arFields)) {
												$mResult = $ID;
											} else {
												$mResult = NULL;
												CWDA::Log('Add element error: '.$IBlockElement->LAST_ERROR.', fields: '.print_r($arFields,1));
											}
											unset($IBlockElement);
										}
									}
								}
								break;
							case 'G':
								if(is_array($arValue['SECTION_ID'])) { // G => G
									$mResult = $arValue['SECTION_ID'][$intIndex];
								} else { // * => G
									if(!empty($mValue) && !empty($arProperty['LINK_IBLOCK_ID'])) {
										$arValue['VALUES'] = array();
										$arValue['SECTION_ID'] = array();
										$resSection = CIBlockSection::GetList(array(),array('IBLOCK_ID'=>$arProperty['LINK_IBLOCK_ID'],'=NAME'=>$mValue),false,false,array('ID','NAME'));
										if($arSection = $resSection->GetNext(false,false)){
											$mResult = $arSection['ID'];
										} else {
											$strCode = CUtil::Translit($mValue, 'ru', array(
												'max_len' => 100,
												'change_case' => 'L',
												'replace_space' => '_',
												'replace_other' => '_',
												'delete_repeat_replace' => true,
											));
											$arFields = array(
												'IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'],
												'NAME' => $mValue,
												'CODE' => $strCode,
											);
											if($arValue['SECTION_ID'][$intIndex]>0){
												$arFields['EXTERNAL_ID'] = $arValue['SECTION_ID'][$intIndex];
											}
											$IBlockSection = new CIBlockSection;
											if ($ID = $IBlockSection->Add($arFields)) {
												$mResult = $ID;
											} else {
												$mResult = NULL;
												CWDA::Log('Add section error: '.$IBlockSection->LAST_ERROR.', fields: '.print_r($arFields,1));
											}
											unset($IBlockSection);
										}
									}
								}
								break;
						}
						//
						return $mResult;
					});
					// Save prepared value
					$arSaveArray = array(
						$arProperty['ID'] => $arPropValue,
					);
					CIBlockElement::SetPropertyValuesEx($arElement['ID'],$arElement['IBLOCK_ID'],$arSaveArray);
					static::resetFacetIndex($arElement['IBLOCK_ID'], $arElement['ID']);
					break;
				}
			}
		} elseif(in_array($Target,array('NAME','CODE','XML_ID','SORT','PREVIEW_TEXT_TYPE','DETAIL_TEXT_TYPE','SHOW_COUNTER','TAGS'))){ // Simple string value
			$mValue = self::ToSingle($arValue,false,$Params);
			$arFields = array(
				$Target => $mValue,
			);
			if(!$IBlockElement->Update($arElement['ID'],$arFields)) {
				$bResult = false;
				CWDA::Log('Update error: '.$IBlockElement->LAST_ERROR.', fields: '.print_r($arFields,1));
			}
		} elseif($Target=='ACTIVE'){
			$arFields = array();
			if(isset($arValue['CHECKBOX_VALUE'])) {
				$arFields[$Target] = self::ToSingle($arValue,'CHECKBOX_VALUE',$Params);
			} else {
				$arFields[$Target] = self::ToSingle($arValue,false,$Params);
			}
			if(!$IBlockElement->Update($arElement['ID'],$arFields)) {
				$bResult = false;
				CWDA::Log('Update error: '.$IBlockElement->LAST_ERROR.', fields: '.print_r($arFields,1));
			}
		} elseif(in_array($Target,array('PREVIEW_TEXT','DETAIL_TEXT'))){ // Preview text || detail text
			$arFields = array(
				$Target => self::ToSingle($arValue,false,$Params),
				$Target.'_TYPE' => self::ToSingle($arValue,'TEXT_TYPE',$Params),
			);
			if(!$IBlockElement->Update($arElement['ID'],$arFields)) {
				$bResult = false;
				CWDA::Log('Update error: '.$IBlockElement->LAST_ERROR.', fields: '.print_r($arFields,1));
			}
		} elseif(in_array($Target,array('PREVIEW_PICTURE','DETAIL_PICTURE'))){ // Preview picture || detail picture
			$mValue = self::ToSingle($arValue,false,$Params);
			if(strlen($mValue) && is_file($_SERVER['DOCUMENT_ROOT'].$mValue)) {
				$arFields = array(
					$Target => CFile::MakeFileArray($mValue),
				);
				if(!$IBlockElement->Update($arElement['ID'],$arFields)) {
					$bResult = false;
					CWDA::Log('Update error: '.$IBlockElement->LAST_ERROR.', fields: '.print_r($arFields,1));
				}
			}
		} elseif(in_array($Target,array('DATE_ACTIVE_FROM','DATE_ACTIVE_TO','DATE_CREATE','TIMESTAMP_X'))){ // Date
			$mValue = self::ToSingle($arValue,false,$Params);
			if(strlen($mValue) && CheckDateTime($mValue)){
				$arFields = array(
					$Target => $mValue,
				);
				if(!$IBlockElement->Update($arElement['ID'],$arFields)) {
					$bResult = false;
					CWDA::Log('Update error: '.$IBlockElement->LAST_ERROR.', fields: '.print_r($arFields,1));
				}
			}
		} elseif(preg_match('#^CATALOG_PRICE_(\d+)$#',$Target,$M)) {
			CWDA::SetProductPrice($arElement['ID'],$M[1],self::ToSingle($arValue,false,$Params));
		} elseif(preg_match('#^CATALOG_STORE_(\d+)$#',$Target,$M)) {
			CWDA::SetProductStoreQuantity($arElement['ID'],$M[1],self::ToSingle($arValue,false,$Params));
		} elseif(preg_match('#^CATALOG_([A-z0-9-_]+)$#',$Target,$M)) {
			$strCode = $M[1];
			$mValue = self::ToSingle($arValue,false,$Params);
			switch($strCode){
				case 'QUANTITY':
				case 'QUANTITY_RESERVED':
				case 'PURCHASING_PRICE':
				case 'WEIGHT':
				case 'WIDTH':
				case 'HEIGHT':
				case 'LENGTH':
					$mValue = FloatVal($mValue);
					CCatalogProduct::Add(array(
						'ID' => $arElement['ID'],
						$strCode => $mValue,
					));
					break;
				case 'QUANTITY_TRACE':
				case 'CAN_BUY_ZERO':
				case 'SUBSCRIBE':
					$mValue = self::ToSingle($arValue,'CHECKBOX_VALUE',$Params);
					if(strlen($mValue)) {
						CCatalogProduct::Add(array(
							'ID' => $arElement['ID'],
							$strCode => self::GetCheckboxValue($mValue,true),
						));
					}
					break;
				case 'VAT_INCLUDED':
					$mValue = self::ToSingle($arValue,'CHECKBOX_VALUE',$Params);
					if(strlen($mValue)) {
						CCatalogProduct::Add(array(
							'ID' => $arElement['ID'],
							$strCode => self::GetCheckboxValue($mValue),
						));
					}
					break;
				case 'VAT_ID': // If not exists, new value is not adding!
					$mValue = FloatVal(str_replace(',','.',$mValue));
					$intVatID = false;
					$arVatList = CWDA::GetVatList(false);
					foreach($arVatList as $arVatItem){
						$arVatItemNoID = $arVatItem;
						if($mValue==FloatVal($arVatItem['RATE'])) {
							$intVatID = $arVatItem['ID'];
							break;
						}
					}
					if($intMeasureID!==false) {
						CCatalogProduct::Add(array(
							'ID' => $arElement['ID'],
							$strCode => $intVatID,
						));
					}
					break;
				case 'MEASURE': // If not exists, new value is not adding!
					$intMeasureID = false;
					$arMeasureList = CWDA::GetMeasureList();
					foreach($arMeasureList as $arMeasureItem){
						$arMeasureItemNoID = $arMeasureItem;
						unset($arMeasureItemNoID['ID']);
						foreach($arMeasureItemNoID as $Key => $Value){
							$arMeasureItemNoID[$Key] = ToLower($Value);
						}
						if(in_array(ToLower($mValue),$arMeasureItemNoID)) {
							$intMeasureID = $arMeasureItem['ID'];
							break;
						}
					}
					if($intMeasureID!==false) {
						CCatalogProduct::Add(array(
							'ID' => $arElement['ID'],
							$strCode => $intMeasureID,
						));
					}
					break;
				case 'MEASURE_RATIO':
					$mValue = FloatVal(str_replace(',','.',$mValue));
					if($mValue>0) {
						$resRatio = CCatalogMeasureRatio::GetList(array(),array('PRODUCT_ID'=>$arElement['ID']));
						if($arRatio = $resRatio->GetNext(false,false)) {
							CCatalogMeasureRatio::Update($arRatio['ID'],array('RATIO'=>$mValue));
						} else {
							CCatalogMeasureRatio::Add(array('PRODUCT_ID'=>$arElement['ID'],'RATIO'=>$mValue));
						}
					}
					break;
				case 'BARCODE':
					$arNewBarcodes = explode(',',$mValue);
					foreach($arNewBarcodes as $key => $strBarcode) {
						$strBarcode = trim($strBarcode);
						if(strlen($strBarcode)) {
							$arNewBarcodes[$key] = $strBarcode;
						} else {
							unset($arNewBarcodes[$key]);
						}
					}
					// Get current product barcodes
					$arCurrentBarcodes = array();
					$resBarcode = CCatalogStoreBarCode::GetList(array(),array('PRODUCT_ID'=>$arElement['ID']));
					while($arBarcode = $resBarcode->GetNext(false,false)) {
						$arCurrentBarcodes[$arBarcode['ID']] = $arBarcode['BARCODE'];
					}
					// Just add new
					foreach($arNewBarcodes as $strNewBarcode){
						$intExistBarcode = array_search($strNewBarcode,$arCurrentBarcodes);
						if($intExistBarcode===false) {
							CCatalogStoreBarCode::Add(array('PRODUCT_ID'=>$arElement['ID'],'BARCODE'=>$strNewBarcode));
						} else {
							unset($arCurrentBarcodes[$intExistBarcode]);
						}
					}
					// Just remove old
					foreach($arCurrentBarcodes as $intBarcodeID => $strBarcode){
						CCatalogStoreBarCode::Delete($intBarcodeID);
					}
					break;
			}
		} elseif (preg_match('#^SEO_([\w\d_]+)$#',$Target,$M)) {
			$arFields = array(
				'IPROPERTY_TEMPLATES' => array(
					$M[1] => self::ToSingle($arValue,false,$Params),
				),
			);
			if (!$IBlockElement->Update($arElement['ID'],$arFields)) {
				$bResult = false;
				CWDA::Log('Error update element #'.$arElement['ID'].', ['.$IBlockElement->LAST_ERROR.'] fields: '.print_r($arFields,1));
			}
		}
		unset($IBlockElement);
		//
		return $bResult;
	}
}
?>