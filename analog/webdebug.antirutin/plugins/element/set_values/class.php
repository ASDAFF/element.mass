<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class SetValues extends PluginElement {
	
	const SAVE_CURRENT_VALUES = 'save_current_values';
	const REMOVE_DUPLICATES = 'remove_duplicates';
	const REMOVE_FILES_IF_EMPTY_VALUE = 'remove_files_if_empty_value';
	
	const INPUT_VALUE = 'value';
	const INPUT_DESCRIPTION = 'value_description';
	
	const PHP_DEBUG_MODE_RESULT = '#PHP_DEBUG_MODE_RESULT#';
	
	protected $arJs = [
		'/bitrix/js/aspro.next/customfilter_control.min.js',
		'/bitrix/js/aspro.max/customfilter_control.min.js',
		'/bitrix/js/aspro.max/property/customfilter_control.js',
		'/bitrix/js/aspro.lite/customfilter_control.min.js',
		'/bitrix/js/aspro.lite/property/customfilter_control.js',
		'/bitrix/js/aspro.max/property/regionphone.js',
		'/bitrix/js/aspro.max/sort/Sortable.min.js',
		'/bitrix/js/aspro.max/iconset.min.js',
		'/bitrix/js/iblock/iblock_edit.js',
	];
	
	protected $arCss = [
		'/bitrix/css/aspro.max/property/regionphone.css',
		'/bitrix/css/aspro.max/iconset.css',
	];
	
	protected $arFieldsFilter = [
		'FIELDS' => ['IS_WRITEABLE' => 'Y', '!CODE' => 'IBLOCK_SECTION_ID'],
		'PROPERTIES' => ['IS_WRITEABLE' => 'Y'],
		'SEO' => ['IS_WRITEABLE' => 'Y'],
		'CATALOG' => ['IS_WRITEABLE' => 'Y'],
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
		#
		'OFFERS.FIELDS' => ['IS_WRITEABLE' => 'Y', '!CODE' => 'IBLOCK_SECTION_ID'],
		'OFFERS.PROPERTIES' => ['IS_WRITEABLE' => 'Y'],
		'OFFERS.SEO' => ['IS_WRITEABLE' => 'Y'],
		'OFFERS.CATALOG' => ['IS_WRITEABLE' => 'Y'],
		'OFFERS.PRICES' => ['IS_WRITEABLE' => 'Y'],
	];
	
	protected $intTmpIBlockId;
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$strField = $this->get('field');
		if(!strlen($strField)){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		$bResult = false;
		if($strOfferField = $this->isOfferField($strField)){
			$strField = $strOfferField;
			$arOffers = $this->getElementOffers($intElementId);
			if(!empty($arOffers)){
				$arCatalog = Helper::getCatalogArray($this->intIBlockId);
				$intIBlockOffersId = $arCatalog['OFFERS_IBLOCK_ID'];
				foreach($arOffers as $intOfferId){
					$mValue = $this->getValueForSave($intOfferId, $strField, true);
					if($mValue === static::PHP_DEBUG_MODE_RESULT){
						$bResult = true;
					}
					else{
						$this->processField($intOfferId, $strField, $mValue, $this->get('description'), $intIBlockOffersId);
					}
				}
				$bResult = true;
			}
			else{
				$bResult = true;
			}
		}
		else{
			$mValue = $this->getValueForSave($intElementId, $strField, false);
			if($mValue === static::PHP_DEBUG_MODE_RESULT){
				$bResult = true;
			}
			else{
				$bResult = $this->processField($intElementId, $strField, $mValue, $this->get('description'));
			}
		}
		return $bResult;
	}
	
	/**
	 *	Process single field
	 */
	protected function processField($intElementId, $strField, $mValue, $mDescription, $intIBlockId=null){
		$intIBlockId = $intIBlockId ? $intIBlockId : $this->intIBlockId;
		if($this->isField($strField)){
			return $this->processElementField($intElementId, $strField, $mValue, $mDescription);
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			return $this->processElementProperty($intElementId, $intIBlockId, $intPropertyId, $mValue, $mDescription);
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			return $this->processElementSeoField($intElementId, $strSeoField, $mValue);
		}
		elseif($intPriceId = $this->isPrice($strField)){
			$strCurrency = $this->get('currency');
			return $this->processElementPrice($intElementId, $intPriceId, $mValue, $strCurrency);
		}
		elseif($intStoreId = $this->isStoreAmount($strField)){
			return $this->processElementStoreAmount($intElementId, $intStoreId, $mValue);
		}
		elseif($strCatalogField = $this->isCatalogField($strField)){
			return $this->processElementCatalogField($intElementId, $strCatalogField, $mValue);
		}
		$this->log(static::getMessage('LOG_FIELD_UNKNOWN', ['#FIELD#' => $strField]));
		return false;
	}
	
	/**
	 *	Process field
	 */
	protected function processElementField($intElementId, $strField, $mValue, $mDescription=null){
		switch($strField){
			case 'NAME':
				$bResult = $this->update($intElementId, ['NAME' => $mValue]);
				break;
			case 'DATE_CREATE':
				$bResult = $this->update($intElementId, ['DATE_CREATE' => $mValue]);
				break;
			case 'ACTIVE':
				$bResult = $this->update($intElementId, ['ACTIVE' => ($mValue == 'Y' ? 'Y' : 'N')]);
				break;
			case 'ACTIVE_FROM':
				$bResult = $this->update($intElementId, ['ACTIVE_FROM' => $mValue]);
				break;
			case 'ACTIVE_TO':
				$bResult = $this->update($intElementId, ['ACTIVE_TO' => $mValue]);
				break;
			case 'SORT':
				$bResult = $this->update($intElementId, ['SORT' => $mValue]);
				break;
			case 'PREVIEW_TEXT':
			case 'DETAIL_TEXT':
				$arFields = [
					$strField => $mValue,
				];
				if(in_array($this->get('type'), ['text', 'html'])){
					$arFields[$strField.'_TYPE'] = $this->get('type');
				}
				$bResult = $this->update($intElementId, $arFields);
				break;
			case 'PREVIEW_TEXT_TYPE':
			case 'DETAIL_TEXT_TYPE':
				$bResult = $this->update($intElementId, [$strField => $mValue]);
				break;
			case 'PREVIEW_PICTURE':
			case 'DETAIL_PICTURE':
				if(is_array($mValue) && isset($mValue['name']) && isset($mValue['tmp_name']) && isset($mValue['type'])){
					$mDescription = is_array($mDescription) ? implode(' ', $mDescription) : $mDescription;
					if(strlen($mDescription)){
						$mValue['description'] = $mDescription;
					}
					$bResult = $this->update($intElementId, [$strField => $mValue]);
				}
				else{
					$mValue = is_array($mValue) ? reset($mValue) : $mValue;
					if(strlen($mValue)){
						if(is_file(Helper::root().$mValue)){
							$mValue = \CFile::makeFileArray(Helper::root().$mValue);
							if(is_array($mValue) && $mValue['size'] > 0){
								$mDescription = is_array($mDescription) ? implode(' ', $mDescription) : $mDescription;
								if(strlen($mDescription)){
									$mValue['description'] = $mDescription;
									$this->replaceMacrosInFileDescription($mValue['description'], $intElementId);
								}
								$bResult = $this->update($intElementId, [$strField => $mValue]);
							}
						}
					}
					elseif($this->get(static::REMOVE_FILES_IF_EMPTY_VALUE) == 'Y'){
						$bResult = $this->update($intElementId, [$strField => ['del' => 'Y']]);
					}
				}
				break;
			case 'CODE':
				$bResult = $this->update($intElementId, ['CODE' => $mValue]);
				break;
			case 'EXTERNAL_ID':
			case 'XML_ID':
				$bResult = $this->update($intElementId, ['EXTERNAL_ID' => $mValue]);
				break;
			case 'TAGS':
				$bResult = $this->update($intElementId, ['TAGS' => $mValue]);
				break;
			case 'CREATED_BY':
				$bResult = $this->update($intElementId, ['CREATED_BY' => $mValue]);
				break;
			case 'MODIFIED_BY':
				$bResult = $this->update($intElementId, ['MODIFIED_BY' => $mValue]);
				break;
		}
		return $bResult;
	}
	
	/**
	 *	Process property
	 */
	protected function processElementProperty($intElementId, $intIBlockId, $intPropertyId, $mValue, $mDescription=null){
		$arProperty = $this->getProperty($intPropertyId, true, $intIBlockId);
		if(!is_array($arProperty)){
			$this->log(static::getMessage('LOG_PROPERTY_NOT_FOUND', ['#IBLOCK_ID#' => $intIBlockId, 
				'#PROPERTY_ID#' => $intPropertyId]));
			return false;
		}
		switch($arProperty['PROPERTY_TYPE']){
			case 'S':
				return $this->processElementPropertyS($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription);
				break;
			case 'N':
				return $this->processElementPropertyN($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription);
				break;
			case 'L':
				return $this->processElementPropertyL($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription);
				break;
			case 'E':
				return $this->processElementPropertyE($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription);
				break;
			case 'G':
				return $this->processElementPropertyG($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription);
				break;
			case 'F':
				return $this->processElementPropertyF($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription);
				break;
		}
		return false;
	}
	protected function processElementPropertyS($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription=null){
		$this->mergeValuesWithDescriptions($arProperty, $mValue, $mDescription);
		/*
		case 'Money':
		case 'HTML':
		case 'video':
		case 'Date':
		case 'DateTime':
		case 'map_yandex':
		case 'map_google':
		case 'UserID':
		case 'TopicID':
		case 'FileMan':
		case 'ElementXmlID':
		case 'directory':
		*/
		switch($arProperty['USER_TYPE']){
			case 'HTML':
				$strType = toLower($this->get('type'));
				$strType = in_array($strType, ['text', 'html']) ? $strType : 'text';
				if(is_array($mValue)){
					if(isset($mValue['VALUE']) && is_string($mValue['VALUE'])){
						$mValue['VALUE'] = [
							'TEXT' => $mValue['VALUE'],
							'TYPE' => $strType,
						];
					}
				}
				$this->setPropertyValue($intElementId, $arProperty['ID'], $mValue, null, $intIBlockId);
				break;
			case 'Money':
				if(is_array($mValue)){
					$mValueTmp = [];
					foreach($mValue as $intValueKey => $strValue){
						$mValueTmp[] = sprintf('%s|%s', $strValue, $this->get('currency', $intValueKey));
					}
					$mValue = $mValueTmp;
				}
				else{
					$mValue = sprintf('%s|%s', $mValue, $this->get('currency'));
				}
				$this->setPropertyValue($intElementId, $arProperty['ID'], $mValue, null, $intIBlockId);
				break;
			case 'map_yandex':
			case 'map_google':
				if(is_array($mValue)){
					$mValueTmp = [];
					foreach($mValue as $intValueKey => $strValue){
						$mValueTmp[] = sprintf('%s,%s', $this->get('lat', $intValueKey), $this->get('long', $intValueKey));
					}
					$mValue = $mValueTmp;
				}
				else{
					$mValue = sprintf('%s,%s', $this->get('lat'), $this->get('long'));
				}
				$this->setPropertyValue($intElementId, $arProperty['ID'], $mValue, null, $intIBlockId);
				break;
			case 'gtable':
				parse_str($mValue, $arSavedValue);
				if(is_array($arSavedValue) && !empty($arSavedValue)){
					$arValue = $arSavedValue['PROP'][$arProperty['ID']];
					if(is_array($arValue) && $arProperty['MULTIPLE'] == 'Y'){
						$arValueTmp = [];
						$intIndex = 0;
						foreach($arValue as $key => $arValueItem){
							if(is_array($arValueItem) && preg_match('#^n?[\d]+$#', $key)){ # allowed values looks like '0' and 'n0'
								unset($arValueItem['GTDELETE']);
								$arValueTmp['n'.$intIndex++] = ['VALUE' => $arValueItem];
							}
						}
						$arValue = $arValueTmp;
					}
					$this->setPropertyValue($intElementId, $arProperty['ID'], $arValue, null, $intIBlockId);
				}
				break;
			case 'SAsproMaxRegionPhone':
				try{
					$arPhones = array_filter(array_map(function($arPhone){
						$arPhone['ICON'] = $arPhone['ICON'] ?? '';
						$arPhone['PHONE'] = $arPhone['PHONE'] ?? '';
						$arPhone['HREF'] = $arPhone['HREF'] ?? '';
						if($arPhone['ICON'] == '' && $arPhone['PHONE'] == '' && $arPhone['HREF'] == ''){
							return null;
						}
						return [
							'VALUE' => \WD\Antirutin\Json::encode([
								'ICON' => $arPhone['ICON'],
								'PHONE' => $arPhone['PHONE'],
								'HREF' => $arPhone['HREF'],
							]),
							'DESCRIPTION' => $arPhone['DESCRIPTION'],
						];
					}, \WD\Antirutin\Json::decode($mValue['VALUE'])));
					$this->setPropertyValue($intElementId, $arProperty['ID'], null, [$arProperty['ID'] => $arPhones], $intIBlockId);
				}
				catch(\Throwable $obError){
					$this->setError('Error set aspro phones: '.$obError->getMessage());
					return false;
				}
				break;
			default:
				$this->setPropertyValue($intElementId, $arProperty['ID'], $mValue, null, $intIBlockId);
				break;
		}
		return true;
	}
	protected function processElementPropertyN($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription=null){
		$mValue = is_array($mValue) ? $mValue : [$mValue];
		$this->mergeValuesWithDescriptions($arProperty, $mValue, $mDescription);
		$this->setPropertyValue($intElementId, $arProperty['ID'], $mValue, null, $intIBlockId);
		return true;
	}
	protected function processElementPropertyL($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription=null){
		$mValue = is_array($mValue) ? $mValue : [$mValue];
		$this->setPropertyValue($intElementId, $arProperty['ID'], $mValue, null, $intIBlockId);
		return true;
	}
	protected function processElementPropertyE($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription=null){
		$mValue = is_array($mValue) ? $mValue : [$mValue];
		$this->setPropertyValue($intElementId, $arProperty['ID'], $mValue, null, $intIBlockId);
		return true;
	}
	protected function processElementPropertyG($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription=null){
		$mValue = is_array($mValue) ? $mValue : [$mValue];
		$this->setPropertyValue($intElementId, $arProperty['ID'], $mValue, null, $intIBlockId);
		return true;
	}
	protected function processElementPropertyF($intElementId, $intIBlockId, $arProperty, $mValue, $mDescription=null){
		$mValue = is_array($mValue) ? $mValue : [$mValue];
		$mDescription = is_array($mDescription) ? $mDescription : [$mDescription];
		$mValueTmp = [];
		if(is_array($mValue)){
			foreach($mValue as $key => $value){
				if(!strlen($value)){
					unset($mValue[$key]);
				}
			}
		}
		if(!empty($mValue)){
			foreach($mValue as $intValueKey => $strValue){
				if(strlen($strValue) && is_file(Helper::root().$strValue)){
					$arFile = \CFile::makeFileArray(Helper::root().$strValue);
					if(is_array($arFile)){
						if(strlen($mDescription[$intValueKey])){
							$arFile['description'] = $mDescription[$intValueKey];
							$this->replaceMacrosInFileDescription($arFile['description'], $intElementId);
						}
						$mValueTmp[] = $arFile;
					}
				}
			}
		}
		elseif($this->get(static::REMOVE_FILES_IF_EMPTY_VALUE) == 'Y'){
			$arFeatures = [
				'PROPERTY_ID' => [$arProperty['ID']],
				'EMPTY_PROPERTIES' => true,
			];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures, true);
			$arPropValue = $arElement['PROPERTIES'][$arProperty['ID']];
			if(is_array($arPropValue)){
				$arPropValue['PROPERTY_VALUE_ID'] = is_array($arPropValue['PROPERTY_VALUE_ID']) 
					? $arPropValue['PROPERTY_VALUE_ID'] : [$arPropValue['PROPERTY_VALUE_ID']];
				$mValueTmp = [];
				foreach($arPropValue['PROPERTY_VALUE_ID'] as $intValueId){
					if($intValueId){
						$mValueTmp[] = [
							'name' => '',
							'type' => '',
							'tmp_name' => '',
							'error' => 0,
							'size' => 0,
							'del' => 'Y',
							'old_id' => $intValueId,
						];
					}
				}
			}
		}
		$this->setPropertyValue($intElementId, $arProperty['ID'], $mValueTmp, null, $intIBlockId);
		return true;
	}
	
	/**
	 *	Transform value to ['VALUE' => '...', 'DESCRIPTION' => '...']
	 */
	protected function mergeValuesWithDescriptions($arProperty, &$mValue, $mDescription){
		if(!empty($mDescription)){
			if(is_array($mValue) && is_array($mDescription)){
				foreach($mValue as $key => $value){
					$mValue[$key] = ['VALUE' => $value, 'DESCRIPTION' => $mDescription[$key]];
				}
			}
			else{
				$mValue = ['VALUE' => $mValue, 'DESCRIPTION' => $mDescription];
			}
		}
	}
		
	/**
	 *	Process seo-field
	 */
	protected function processElementSeoField($intElementId, $strSeoField, $mValue){
		$strSeoFieldFull = IBlock::$arSeoMapElement[$strSeoField];
		$arFields = [
			'IPROPERTY_TEMPLATES' => [
				$strSeoFieldFull => $mValue,
			],
		];
		$bResult = $this->update($intElementId, $arFields, false, false, false);
		return $bResult;
	}
	/**
	 *	Process price
	 */
	protected function processElementPrice($intElementId, $intPriceId, $fPrice, $strCurrency){
		if($this->get('price_mode') == 'markup'){
			return Helper::setProductPriceExtra($intElementId, $intPriceId, $this->get('markup'));
		}
		elseif($this->get('price_mode') == 'interval'){
			return Helper::setProductPriceInterval($intElementId, $intPriceId, $this->getIntervalPrices());
		}
		else{
			if($fPrice === 'delete'){
				return Helper::deleteProductPrice($intElementId, $intPriceId);
			}
			return !!Helper::setProductPrice($intElementId, $intPriceId, $fPrice, $strCurrency);
		}
	}
	
	/**
	 *	Process store amount
	 */
	protected function processElementStoreAmount($intElementId, $intStoreId, $mValue){
		return Helper::setProductStoreAmount($intElementId, $intStoreId, $mValue);
	}
	
	/**
	 *	Process catalog field
	 */
	protected function processElementCatalogField($intElementId, $strField, $mValue){
		if($strField == 'MEASURE_ID'){
			$strField = 'MEASURE';
		}
		elseif($strField == 'PURCHASING_PRICE'){
			$strField = [
				'PURCHASING_PRICE' => $mValue,
				'PURCHASING_CURRENCY' => $this->get('currency'),
			];
			$mValue = null;
		}
		if($strField == 'BARCODE'){
			return Helper::setProductBarcodes($intElementId, $mValue, $this->isSaveValues());
		}
		elseif($strField == 'MEASURE_RATIO'){
			return Helper::setProductMeasureRatio($intElementId, $mValue);
		}
		elseif($strField == 'PRODUCT_GROUP'){
			return Helper::setProductField($intElementId, 'UF_PRODUCT_GROUP', $mValue);
		}
		else{
			if($strField == 'NEGATIVE_AMOUNT_TRACE'){
				$strField = [ // Bitrix bugfix, 2020-04-01
					'NEGATIVE_AMOUNT_TRACE' => $mValue,
					'CAN_BUY_ZERO' => $mValue,
				];
				$mValue = null;
			}
			return Helper::setProductField($intElementId, $strField, $mValue);
		}
	}
	
	/**
	 *	Is checkbox 'save values' checked?
	 */
	protected function isSaveValues(){
		return $this->get(static::SAVE_CURRENT_VALUES) == 'Y';
	}
	
	/**
	 *	Is checkbox 'remove duplicates' checked?
	 */
	protected function isRemoveDuplicates(){
		return $this->get(static::REMOVE_DUPLICATES) == 'Y';
	}
	
	/**
	 *	Is checkbox 'remove files if empty value' checked?
	 */
	protected function isRemoveFilesIfEmptyValue(){
		return $this->get(static::REMOVE_FILES_IF_EMPTY_VALUE) == 'Y';
	}
	
	// *******************************************************************************************************************
	// Other methods
	// *******************************************************************************************************************
	
	/**
	 *	AJAX: Load field input
	 */
	protected function loadFieldInput(&$arJson){
		if(!strlen($this->arPost['field'])){
			return static::getMessage('ERROR_NO_FIELD');
		}
		return $this->getInputHtml($this->arPost['field'], $arValues=null, $arDescriptions=null, true);
	}
	
	/**
	 *	Get HTML for one input with values
	 */
	protected function getInputHtml($strField, $arValues=[], $arDescriptions=[], $bAjax=false){
		if($strOfferField = $this->isOfferField($strField)){
			$strFieldOriginal = $strField;
			$strField = $strOfferField;
			$this->setIBlockId($this->replaceIBlockId());
		}
		$arField = $this->getFields($this->arFieldsFilter, $strField);
		if(!is_array($arField) || empty($arField)){
			if($strOfferField){
				$this->restoreIBlockId();
			}
			return static::getMessage('ERROR_FIELD_NOT_FOUND');
		}
		$arValues = is_array($arValues) ? $arValues : [$arValues];
		$arDescriptions = is_array($arDescriptions) ? $arDescriptions : [$arDescriptions];
		$bSupported = false;
		$strFile = __DIR__.'/include/field/'.toLower(str_replace(':', '_', $arField['TYPE_FULL'])).'.php';
		$strFileField = __DIR__.'/include/_field.php';
		Helper::loadMessages($strFileField);
		$strFieldsHtml = '';
		if(is_file($strFile)){
			Helper::loadMessages($strFile);
			$bSupported = true;
			$strId = $arField['DATA']['ID'];
			$bMultiple = $bAllowMultiply = $arField['IS_MULTIPLE'] == 'Y';
			$bRequired = $arField['DATA']['IS_REQUIRED'] == 'Y' ? true : false;
			$strDefaultValue = $arField['DATA']['DEFAULT_VALUE'];
			$strHint = $arField['DATA']['HINT'];
			if($strField == 'CATALOG_BARCODE'){
				$intColCount = 30;
				$intRowCount = 1;
			}
			else{
				$intColCount = is_numeric($arField['DATA']['COL_COUNT']) ? $arField['DATA']['COL_COUNT'] : 50;
				$intRowCount = is_numeric($arField['DATA']['ROW_COUNT']) ? $arField['DATA']['ROW_COUNT'] : 5;
			}
			foreach($arValues as $intValueKey => $mValue){
				$mDescription = $arDescriptions[$intValueKey];
				ob_start();
					require($strFile);
				$strFieldHtml = ob_get_clean();
				ob_start();
					require($strFileField);
				$strFieldsHtml .= ob_get_clean();
				if(!$bAllowMultiply){
					break;
				}
			}
		}
		$strFieldHtml = static::getMessage('SELECT_FIELD_PREASE');
		if($bSupported){
			$bCanSaveCurrentValues = $bMultiple;
			$bCanRemoveDuplicates = $bMultiple && $arField['TYPE'] != 'F';
			$strFileFields = __DIR__.'/include/_fields.php';
			ob_start();
				Helper::loadMessages($strFileFields);
			require($strFileFields);
			$strFieldHtml = ob_get_clean();
		}
		elseif(strlen($arField['TYPE_FULL'])){
			$strFieldHtml = static::getMessage('FIELD_TYPE_NOT_SUPPORTED', [
				'#FIELD_TYPE#' => $arField['TYPE_FULL'].($arField['DATA']['MULTIPLE'] == 'Y' ? '+' : ''),
			]);
		}
		if($strOfferField){
			$this->restoreIBlockId();
		}
		return $strFieldHtml;
	}
	
	/**
	 *	Temporary replace $this->intIBlockId
	 */
	protected function replaceIBlockId(){
		if(!$this->intTmpIBlockId){
			$arCatalog = Helper::getCatalogArray($this->intIBlockId);
			$intIBlockOffersId = $arCatalog['OFFERS_IBLOCK_ID'];
			$this->setIBlockId($intIBlockOffersId);
			$this->intTmpIBlockId = $intIBlockOffersId;
		}
		return $this->intTmpIBlockId;
	}
	
	/**
	 *	Restore $this->intIBlockId
	 */
	protected function restoreIBlockId(){
		if($this->intTmpIBlockId){
			$this->setIBlockId($this->intTmpIBlockId);
		}
		$intIBlockId = $this->getIBlockId();
		$arCatalog = Helper::getCatalogArray($intIBlockId);
		$intIBlockOffersId = $arCatalog['OFFERS_IBLOCK_ID'];
		$this->setIBlockId($intIBlockOffersId);
		return $intIBlockOffersId;
	}
	
	/**
	 *	Get save value
	 */
	protected function getValueForSave($intElementId, $strField, $bOffer){
		$mValue = null;
		if($this->get('use_custom_php') == 'Y'){
			$strCode = $this->get('php_code');
			if(strlen($strCode)){
				$intIBlockId = $this->intIBlockId;
				if($bOffer){
					$arCatalog = Helper::getCatalogArray($this->intIBlockId);
					$intIBlockIdMain = $intIBlockId;
					$intIBlockIdOffers = $intIBlockId = $arCatalog['OFFERS_IBLOCK_ID'];
				}
				$value = $this->getValue($intElementId, $intIBlockId, $strField, $bOffer);
				$arElement = IBlock::getElementArray($intElementId, $intIBlockId, true, false);
				$strCode = 'namespace WD\Antirutin;'.PHP_EOL.$strCode.';';
				$mValue = eval($strCode);
				if($this->get('php_debug_mode') == 'Y'){
					$this->debug($mValue);
					$mValue = static::PHP_DEBUG_MODE_RESULT;
					$this->setBreaked(true);
				}
			}
		}
		else{
			$mValue = $this->get('value');
		}
		return $mValue;
	}

	/**
	 * Get current value
	 */
	protected function getValue($intElementId, $intIBlockId, $strField, $bOffer){
		$result = null;
		if($this->isField($strField)){
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, ['FIELDS' => [$strField]], true);
			$result = $arElement[$strField];
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, ['PROPERTY_ID' => true, 'EMPTY_PROPERTIES' => true], true);
			return $arElement['PROPERTIES'][$intPropertyId];
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, ['SEO' => true]);
			return $arElement['SEO'][$strSeoField];
		}
		elseif($intPriceId = $this->isPrice($strField)){
			$arFeatures = ['PRICES' => [$intPriceId]];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
			$result = $arElement['PRICES'][$intPriceId];
		}
		elseif($intStoreId = $this->isStoreAmount($strField)){
			$arFeatures = ['STORES' => [$intStoreId]];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
			$result = $arElement['STORES'][$intStoreId]['AMOUNT'];
		}
		elseif($strCatalogField = $this->isCatalogField($strField)){
			$arFeatures = ['CATALOG' => true, 'BARCODE' => true];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
			$result = $arElement[$strField];
			if($strCatalogField == 'MEASURE_ID'){
				if($intMeasure = $arElement['CATALOG_MEASURE']){
					$result = Helper::getMeasuresList()[$intMeasure];
				}
			}
			elseif($strCatalogField == 'MEASURE_RATIO'){
				if($arRatio = \CCatalogMeasureRatio::getList([], ['PRODUCT_ID' => $intElementId])->fetch()){
					$result = $arRatio['RATIO'];
				}
			}
			elseif($strCatalogField == 'PRODUCT_GROUP'){
				$result = $arElement['PRODUCT_GROUP_NAME'];
			}
			elseif($strCatalogField == 'VAT_ID'){
				$arVatList = Helper::getVatList();
				$result = $arVatList[$arElement[$strField]];
			}
		}
		return $result;
	}
	
	/**
	 *	Get demo code
	 */
	protected function getDemoCode(){
		return static::getMessage('DEMO_CODE');
	}

	/**
	 * Get saved interval prices
	 */
	protected function getIntervalPrices(){
		$arResult = [];
		#
		$arFrom = $this->get('interval_from');
		$arTo = $this->get('interval_to');
		$arPrice = $this->get('interval_price');
		$arCurrency = $this->get('interval_currency');
		#
		if(is_array($arFrom) && is_array($arTo) && is_array($arPrice) && is_array($arCurrency)){
			$bFirst = true;
			foreach($arFrom as $key => $value){
				if($bFirst){
					$bFirst = false;
					continue;
				}
				$arResult[] = [
					'from' => $arFrom[$key],
					'to' => $arTo[$key],
					'price' => $arPrice[$key],
					'currency' => $arCurrency[$key],
				];
			}
		}
		#
		return $arResult;
	}

	/**
	 * 
	 */
	protected function replaceMacrosInFileDescription(&$strDescription, $intElementId){
		if(strpos($strDescription, '#NAME#') !== false){
			$strName = '';
			$arQuery = [
				'filter' => ['ID' => $intElementId],
				'select' => ['NAME'],
			];
			if($arElement = \Bitrix\IBlock\ElementTable::getList($arQuery)->fetch()){
				$strName = $arElement['NAME'];
			}
			$strDescription = str_replace('#NAME#', $strName, $strDescription);
		}
	}
	
}

?>