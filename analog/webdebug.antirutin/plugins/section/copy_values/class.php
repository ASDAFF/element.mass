<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginSection;

class CopyValues extends PluginSection {
	
	const SAVE_CURRENT_VALUES = 'save_current_values';
	const REMOVE_DUPLICATES = 'remove_duplicates';
	const SKIP_NULL_VALUES = 'skip_null_values';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['!CODE' => ['CREATED_BY__NAME', 'MODIFIED_BY__NAME']],
		'PROPERTIES' => ['!TYPE_FULL' => ['S:video']],
		'SEO' => true,
	];
	
	protected $arFieldsFilter2 = [
		'FIELDS' => ['IS_WRITEABLE' => 'Y'],
		'PROPERTIES' => ['IS_WRITEABLE' => 'Y', '!TYPE_FULL' => ['S:video']],
		'IBLOCK' => ['IS_WRITEABLE' => 'Y'],
		'CATALOG' => ['IS_WRITEABLE' => 'Y'],
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
		'SEO' => true,
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		if($this->isEmpty('source')){
			$this->setError(static::getMessage('ERROR_NO_SOURCE_FIELD'));
			return false;
		}
		if($this->isEmpty('target')){
			$this->setError(static::getMessage('ERROR_NO_TARGET_FIELD'));
			return false;
		}
		$arValues = $this->getFieldValue($intSectionId);
		if($this->isSkipNullValues() && $this->checkSourceValueIsEmpty($arValues)){
			$this->log(sprintf('Empty values for section #%s', $intSectionId), true);
			return true;
		}
		$bResult = $this->processField($intSectionId, $arValues);
		return $bResult;
	}
	
	/**
	 *	Include classes
	 */
	public function includeClasses(){
		if(\Bitrix\Main\Loader::includeModule('currency') && class_exists('\CCurrencyLang')){
			require_once($this->strDirectory.'/include/currencylang.php');
		}
	}

	/**
	 * 	Check if source value empty (for prevent save if checkbox checked)
	 */
	protected function checkSourceValueIsEmpty($mValues){
		$mValues = $this->transformObjectToValue($mValues);
		$mValues = is_array($mValues) ? implode('', $mValues) : $mValues;
		$mValues = trim($mValues);
		return strlen($mValues) ? false : true;
	}
	
	// *******************************************************************************************************************
	// Get methods
	// *******************************************************************************************************************
	
	/**
	 *	Include classes
	 */
	protected function getFieldValue($intSectionId){
		$arResult = [];
		$strField = $this->get('source');
		$strHtmlType = null;
		$arField = [];
		if($this->isSectionField($strField)){
			$arSelect = [$strField];
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arSelect);
			$arResult = $this->formatValueField($strField, $arSection);
			if(in_array($strField, ['DESCRIPTION'])){
				$strHtmlType = $arSection[$strField.'_TYPE'];
			}
		}
		elseif($strPropertyCode = $this->isSectionProperty($strField)){
			$arSelect = [$strField];
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arSelect);
			$arProperty = $this->getProperty($strPropertyCode);
			$arProperty = array_merge($arProperty, [
				'VALUE' => $arSection['PROPERTIES'][$strPropertyCode],
				'SECTION_ID' => $intSectionId,
				'TYPE_FULL' => $arProperty['PROPERTY_TYPE'].
					(strlen($arProperty['USER_TYPE']) ? ':'.$arProperty['USER_TYPE'] : ''),
			]);
			$arField = $arProperty;
			$arResult = $this->formatValueProperty($arProperty);
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			$arFeatures = ['SEO' => true];
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arFeatures);
			$arResult[] = $arSection['SEO'][$strSeoField];
		}
		# Transform result array using `Item` class
		$arResultTmp = [];
		foreach($arResult as $key => $value){
			if(is_object($value)){
				$arResultTmp[] = $value;
			}
			else{
				$arValue = [
					'FIELD' => $strField,
					'FIELD_ARRAY' => $arProperty,
					'VALUE' => $value,
				];
				if(is_array($arProperty['VALUE_HTML_TYPE'])){
					$arValue['HTML_TYPE'] = $arProperty['VALUE_HTML_TYPE'][$key];
				}
				elseif($strHtmlType){
					$arValue['HTML_TYPE'] = $strHtmlType;
				}
				if($arProperty['TYPE_FULL'] == 'S:HTML' && is_array($value)){
					$arValue['VALUE'] = toUpper($value['TYPE']) == 'HTML' ? html_entity_decode($value['TEXT']) : $value['TEXT'];
					$arValue['HTML_TYPE'] = $value['TYPE'];
				}
				$arResultTmp[] = new ValueItem($arValue);
			}
		}
		return $arResultTmp;
	}
	
	/**
	 *	Format values (eg, for S:directory: brand123 => 'Samsung')
	 */
	protected function formatValueField($strField, $arSection){
		$arResult = [];
		#
		$arValue = $arSection['~'.$strField];
		$arValue = is_array($arValue) ? $arValue : (!is_null($arValue) && strlen($arValue) ? [$arValue] : []);
		$arPropertyFake = [];
		$arPropertyFake['VALUE_ARRAY'] = $arValue;
		#
		if($strField == 'ACTIVE'){
			$arResult = $this->formatValue_S_checkbox($arPropertyFake);
		}
		elseif(in_array($strField, ['DESCRIPTION'])){
			$strValue = $arSection['~'.$strField];
			switch($this->get('source_s_html')){
				case 'html_to_text':
					$strValue = \HTMLToTxt($strValue, '', [], false);
					break;
				case 'strip_tags':
					$strValue = strip_tags($strValue);
					break;
				default:
					# Nothing
					break;
			}
			$arResult[] = $strValue;
		}
		elseif(in_array($strField, ['PICTURE', 'DETAIL_PICTURE'])){
			if(is_array($arSection[$strField])){
				$arPropertyFake['VALUE_ARRAY'] = [$arSection[$strField]];
				$arResult = $this->formatValue_F($arPropertyFake);
			}
		}
		elseif(in_array($strField, ['CREATED_BY', 'MODIFIED_BY'])){
			$arResult = $this->formatValue_N_userid($arPropertyFake);
		}
		elseif($strField == 'SECTION_PAGE_URL'){
			$arResult[] = $this->formatUrl($arSection[$strField]);
		}
		else{
			$mValue = $arSection['~'.$strField];
			if(is_array($mValue) && !empty($mValue) || is_string($mValue) && strlen($mValue)){
				$arResult[] = $mValue;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Format values (eg, for S:directory: brand123 => 'Samsung')
	 */
	protected function formatValueProperty(&$arProperty){
		$arResult = [];
		#
		$arValue = $arProperty['VALUE'];
		#
		if($arProperty['TYPE_FULL'] == 'S:HTML'){
			$bIsHtmlValue = is_array($arValue) && isset($arValue['TEXT']) && isset($arValue['TYPE']);
			$arValue = (is_array($arValue) && !$bIsHtmlValue) ? $arValue : 
				(!is_null($arValue) && is_array($arValue) ? [$arValue] : []);
		}
		else{
			$arValue = is_array($arValue) ? $arValue : 
				(!is_null($arValue) && strlen($arValue) ? [$arValue] : []);
		}
		$arProperty['VALUE_ARRAY'] = $arValue;
		#
		$arDescr = $arProperty['DESCRIPTION'];
		$arDescr = is_array($arDescr) ? $arDescr : (!is_null($arDescr) && strlen($arDescr) ? [$arDescr] : []);
		$arProperty['VALUE_DESCRIPTION'] =  $arDescr;
		#
		$strType = $arProperty['PROPERTY_TYPE'];
		$strTypeFull = $arProperty['PROPERTY_TYPE'].(strlen($arProperty['USER_TYPE']) ? ':'.$arProperty['USER_TYPE'] : '');
		if($strTypeFull == 'S:directory'){
			$arResult = $this->formatValue_S_directory($arProperty);
		}
		elseif($strType == 'L'){
			$arResult = $this->formatValue_L($arProperty);
		}
		elseif($strType == 'E'){
			$arResult = $this->formatValue_E($arProperty);
		}
		elseif($strType == 'G'){
			$arResult = $this->formatValue_G($arProperty);
		}
		elseif($strType == 'F'){
			$arResult = $this->formatValue_F($arProperty);
		}
		elseif($strTypeFull == 'S:_Checkbox'){
			$arResult = $this->formatValue_S_checkbox($arProperty);
		}
		elseif($strTypeFull == 'S:Money'){
			$arResult = $this->formatValue_S_money($arProperty);
		}
		elseif($strTypeFull == 'S:UserID'){
			$arResult = $this->formatValue_S_userid($arProperty);
		}
		elseif($strType == 'S'){
			$arResult = is_array($arProperty['VALUE_ARRAY']) ? $arProperty['VALUE_ARRAY'] : 
				(strlen($arProperty['VALUE_ARRAY']) ? [$arProperty['VALUE_ARRAY']] : []);
		}
		else{
			$arResult = $arProperty['VALUE_ARRAY'];
		}
		return $arResult;
	}
	
	/**
	 *	Format values for S:directory
	 */
	protected function formatValue_S_directory($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$strEntityDataClass = IBlock::getHighloadClass($arProperty['USER_TYPE_SETTINGS']['TABLE_NAME']);
			if(strlen($strEntityDataClass)){
				$arMap = [
					'title' => 'UF_NAME',
					'id' => 'ID',
					'value' => 'UF_XML_ID',
				];
				$strKey = $arMap[$this->get('source_s_directory')];
				$arSort = [$strKey => 'ASC'];
				$arFilter = ['UF_XML_ID' => $arProperty['VALUE_ARRAY']];
				$arSelect = ['UF_XML_ID', $strKey];
				$resSata = $strEntityDataClass::getlist([
					'order' =>  $arSort,
					'filter' => $arFilter,
					'select' => $arSelect,
				]);
				/*
				$arProperty['VALUE_ARRAY'] = [];
				while($arItem = $resSata->fetch()) {
					$arProperty['VALUE_ARRAY'][] = $arItem[$strKey];
				}
				*/
				while($arItem = $resSata->fetch()) {
					foreach($arProperty['VALUE_ARRAY'] as $key => $value){
						if($value == $arItem['UF_XML_ID']){
							$arProperty['VALUE_ARRAY'][$key] = $arItem[$strKey];
						}
					}
				}
			}
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for S:_Checkbox
	 */
	protected function formatValue_S_checkbox($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			foreach($arProperty['VALUE_ARRAY'] as $key => $value){ // $value = Y, N, or D
				switch($this->get('format_s_checkbox')){
					case 'yes_no':
						$arProperty['VALUE_ARRAY'][$key] = Helper::getMessage('WDA_'.$value);
						break;
					case 'y_n':
						$arProperty['VALUE_ARRAY'][$key] = $value;
						break;
					case '1_0':
						$arProperty['VALUE_ARRAY'][$key] = $value == 'Y' ? '1' : '0';
						break;
				}
			}
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for S:Money
	 */
	protected function formatValue_S_money($arProperty){
		$arResult = [];
		if(!empty($arProperty['VALUE_ARRAY'])){
			foreach($arProperty['VALUE_ARRAY'] as $key => $value){
				$arPrice = explode('|', $value);
				switch($this->get('source_s_money')){
					case 'formatted':
						$arResult[] = Helper::currencyFormat($arPrice[0], $arPrice[1]);
						break;
					case 'price_currency':
						$arResult[] = $value;
						break;
					case 'number':
						$arResult[] = $arPrice[0];
						break;
					default:
						$arValue = [
							'FIELD' => 'PROPERTY_'.$arProperty['ID'],
							'FIELD_ARRAY' => $arProperty,
							'VALUE' => $arPrice[0],
							'CURRENCY' => $arPrice[1],
						];
						$arResult[] = new ValueItem($arValue);
						break;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Format values for S:_CheckboxD
	 */
	protected function formatValue_S_checkboxD($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			foreach($arProperty['VALUE_ARRAY'] as $key => $value){ // $value = Y, N, or D
				switch($this->get('format_s_checkboxd')){
					case 'yes_no':
						$arProperty['VALUE_ARRAY'][$key] = Helper::getMessage('WDA_'.$value);
						break;
					case 'y_n':
						$arProperty['VALUE_ARRAY'][$key] = $value;
						break;
					case '1_0':
						$arProperty['VALUE_ARRAY'][$key] = $value == 'Y' ? '1' : ($value == 'N' ? '0' : '-');
						break;
				}
			}
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for L
	 */
	protected function formatValue_L($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arMap = [
				'value' => 'VALUE_ENUM',
				'xml_id' => 'VALUE_XML_ID',
				'enum_id' => 'VALUE_ENUM_ID',
			];
			$strKey = $arMap[$this->get('source_l')];
			$arResult = $arProperty[$strKey];
			if(!is_array($arResult)){
				$arResult = strlen($arResult) ? [$arResult] : [];
			}
			return $arResult;
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for E
	 */
	protected function formatValue_E($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arResult = [];
			$arFilter = [
				'ID' => $arProperty['VALUE_ARRAY'],
			];
			if($arProperty['LINK_IBLOCK_ID']){
				$arFilter['IBLOCK_ID'] = $arProperty['LINK_IBLOCK_ID'];
			}
			$arMap = [
				'title' => 'NAME',
				'code' => 'CODE',
				'xml_id' => 'XML_ID',
				'id' => 'ID',
			];
			$strKey = $arMap[$this->get('source_e')];
			$arSort = [$strKey => 'ASC'];
			$arSelect = ['ID', $strKey];
			$resElements = \CIBlockElement::getList($arSort, $arFilter, false, false, $arSelect);
			while($arSection = $resElements->getNext()){
				$arResult[] = $arElement[$strKey];
			}
			return $arResult;
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for G
	 */
	protected function formatValue_G($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arResult = [];
			$arFilter = [
				'ID' => $arProperty['VALUE_ARRAY'],
			];
			if($arProperty['LINK_IBLOCK_ID']){
				$arFilter['IBLOCK_ID'] = $arProperty['LINK_IBLOCK_ID'];
			}
			$arMap = [
				'title' => 'NAME',
				'code' => 'CODE',
				'xml_id' => 'XML_ID',
				'id' => 'ID',
			];
			$strKey = $arMap[$this->get('source_g')];
			$arSort = [$strKey => 'ASC'];
			$arSelect = ['ID', $strKey];
			$resSections = \CIBlockSection::getList($arSort, $arFilter, false, $arSelect);
			while($arSection = $resSections->getNext()){
				$arResult[] = $arSection[$strKey];
			}
			return $arResult;
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for F
	 */
	protected function formatValue_F($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arProperty['VALUE_DESCRIPTION'] = is_array($arProperty['VALUE_DESCRIPTION']) ? $arProperty['VALUE_DESCRIPTION'] : [];
			foreach($arProperty['VALUE_ARRAY'] as $key => $arFile){
				if(is_numeric($arFile)){
					$arFile = \CFile::getFileArray($arFile);
				}
				if(is_array($arFile)){
					switch($this->get('source_f')){
						case 'src_rel':
							$arProperty['VALUE_ARRAY'][$key] = $arFile['SRC'];
							break;
						case 'src_abs':
							$arProperty['VALUE_ARRAY'][$key] = Helper::root().$arFile['SRC'];
							break;
						case 'src_url':
							$arProperty['VALUE_ARRAY'][$key] = $this->formatUrl($arFile['SRC']);
							break;
						case 'description':
							$arProperty['VALUE_ARRAY'][$key] = $arFile['DESCRIPTION'];
							break;
						case 'id':
							$arProperty['VALUE_ARRAY'][$key] = $arFile['ID'];
							break;
					}
					$arProperty['VALUE_DESCRIPTION'][$key] = $arFile['DESCRIPTION'];
				}
			}
		}
		$arResult = [];
		foreach($arProperty['VALUE_ARRAY'] as $key => $value){
			$arValue = [
				'FIELD' => $strField,
				'FIELD_ARRAY' => $arProperty,
				'VALUE' => $value,
				'DESCRIPTION' => $arProperty['VALUE_DESCRIPTION'][$key],
			];
			$arResult[] = new ValueItem($arValue);
		}
		return $arResult;
	}
	
	/**
	 *	Format values for N:_UserId
	 */
	protected function formatValue_N_userid($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arResult = [];
			foreach($arProperty['VALUE_ARRAY'] as $intUserId){
				if(is_numeric($intUserId) && $intUserId > 0){
					$strUser = $this->formatUserName($intUserId, $this->get('source_n_userid'));
					if(strlen($strUser)){
						$arResult[] = $strUser;
					}
				}
			}
			return $arResult;
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for S:UserID
	 */
	protected function formatValue_S_userid($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arResult = [];
			foreach($arProperty['VALUE_ARRAY'] as $intUserId){
				if(is_numeric($intUserId) && $intUserId > 0){
					$strUser = $this->formatUserName($intUserId, $this->get('source_s_userid'));
					if(strlen($strUser)){
						$arResult[] = $strUser;
					}
				}
			}
			return $arResult;
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	
	 */
	protected function formatUserName($intUserId, $strFormat){
		$strResult = false;
		$arSelect = ['ID', 'NAME', 'LAST_NAME', 'LOGIN', 'EMAIL'];
		$resUser = \CUser::getList($by='ID', $order='ASC', ['ID' => $intUserId], ['FIELDS' => $arSelect]);
		if($arUser = $resUser->getNext()){
			switch($strFormat){
				case 'default':
					$strResult = sprintf('[%d] (%s) %s', $arUser['ID'], $arUser['LOGIN'], $arUser['NAME']);
					break;
				case 'name_lastname':
					$strResult = sprintf('%s %s', $arUser['NAME'], $arUser['LAST NAME']);
					break;
				case 'name':
					$strResult = $arUser['NAME'];
					break;
				case 'login':
					$strResult = $arUser['LOGIN'];
					break;
				case 'email':
					$strResult = $arUser['EMAIL'];
					break;
				case 'id':
					$strResult = $arUser['ID'];
					break;
			}
		}
		return $strResult;
	}
	
	// *******************************************************************************************************************
	// Set methods
	// *******************************************************************************************************************
	
	/**
	 *	Process single field
	 */
	protected function processField($intSectionId, $arValues){
		$strField = $this->get('target');
		if(is_array($arValues) && empty($arValues)){
			$arValues = false;
		}
		if($this->isSectionField($strField)){
			return $this->processSectionField($intSectionId, $strField, $arValues);
		}
		elseif($strPropertyCode = $this->isSectionProperty($strField)){
			return $this->processSectionProperty($intSectionId, $strPropertyCode, $arValues);
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			return $this->processSectionSeoField($intSectionId, $strSeoField, $arValues);
		}
		$this->log(static::getMessage('LOG_FIELD_UNKNOWN', ['#FIELD#' => $strField]));
		return false;
	}
	
	
	/**
	 *	Process field
	 */
	protected function processSectionField($intSectionId, $strField, $arValues){
		$mSaveValue = $this->transformObjectToValue($arValues, false);
		switch($strField){
			case 'NAME':
				$bResult = $this->update($intSectionId, ['NAME' => $mSaveValue]);
				break;
			case 'DATE_CREATE':
				$bResult = $this->update($intSectionId, ['DATE_CREATE' => $mSaveValue]);
				break;
			case 'ACTIVE':
				$bResult = $this->update($intSectionId, ['ACTIVE' => ($mSaveValue == 'Y' ? 'Y' : 'N')]);
				break;
			case 'SORT':
				$bResult = $this->update($intSectionId, ['SORT' => $mSaveValue]);
				break;
			case 'DESCRIPTION':
				$arFields = [
					$strField => $mSaveValue,
				];
				$strHtmlType = $this->getValueHtmlType($mSaveValue, $this->mObjectValues);
				if($strHtmlType){
					$arFields[$strField.'_TYPE'] = $strHtmlType;
				}
				$bResult = $this->update($intSectionId, $arFields);
				break;
			case 'DESCRIPTION_TYPE':
				$bResult = $this->update($intSectionId, [$strField => $arValues]);
				break;
			case 'PICTURE':
			case 'DETAIL_PICTURE':
				$arFile = $this->makeFileArray($intSectionId, $mSaveValue);
				if(is_array($arFile)){
					$arFile['DESCRIPTION'] = $arFile['description'] = $this->mObjectValues->getDescription();
					$arSaveValue[] = $arFile;
					$bResult = $this->update($intSectionId, [$strField => $arFile]);
				}
				else{
					$bResult = false;
				}
				break;
			case 'CODE':
				$bResult = $this->update($intSectionId, ['CODE' => $mSaveValue]);
				break;
			case 'EXTERNAL_ID':
			case 'XML_ID':
				$bResult = $this->update($intSectionId, ['EXTERNAL_ID' => $mSaveValue]);
				break;
			case 'CREATED_BY':
				$bResult = $this->update($intSectionId, ['CREATED_BY' => $mSaveValue]);
				break;
			case 'MODIFIED_BY':
				$bResult = $this->update($intSectionId, ['MODIFIED_BY' => $mSaveValue]);
				break;
		}
		return $bResult;
	}
	
	/**
	 *	Process property
	 */
	protected function processSectionProperty($intSectionId, $strPropertyCode, $arValues){
		$arProperty = $this->getProperty($strPropertyCode);
		if(!is_array($arProperty)){
			$this->log(static::getMessage('LOG_PROPERTY_NOT_FOUND', ['#IBLOCK_ID#' => $this->intIBlockId, 
				'#PROPERTY_ID#' => $intPropertyId]));
			return false;
		}
		switch($arProperty['PROPERTY_TYPE']){
			case 'S':
				return $this->processSectionPropertyS($intSectionId, $arProperty, $arValues);
				break;
			case 'N':
				return $this->processSectionPropertyN($intSectionId, $arProperty, $arValues);
				break;
			case 'L':
				return $this->processSectionPropertyL($intSectionId, $arProperty, $arValues);
				break;
			case 'E':
				return $this->processSectionPropertyE($intSectionId, $arProperty, $arValues);
				break;
			case 'G':
				return $this->processSectionPropertyG($intSectionId, $arProperty, $arValues);
				break;
			case 'F':
				return $this->processSectionPropertyF($intSectionId, $arProperty, $arValues);
				break;
		}
		return false;
	}
	protected function processSectionPropertyS($intSectionId, $arProperty, $arValues){
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
		*/
		switch($arProperty['USER_TYPE']){
			case 'directory':
				$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y',
					$this->get('multiple_mode'));
				$mSaveValue = is_array($mSaveValue) ? $mSaveValue : (strlen($mSaveValue) ? [$mSaveValue] : []);
				$arSaveValue = [];
				$strTableName = $arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'];
				# Set
				foreach($mSaveValue as $value){
					$arHighloadItem = IBlock::getHighloadItems($strTableName, [], ['UF_NAME' => $value]);
					if(is_array($arHighloadItem) && !empty($arHighloadItem)){
						$arSaveValue[] = key($arHighloadItem);
					}
					else{
						$strXmlId = \CUtil::translit($value, LANGUAGE_ID, [
							'max_len' => 255,
							'change_case' => 'L',
							'replace_space' => '_',
							'replace_other' => '_',
							'delete_repeat_replace' => true,
						]);
						$arItemFields = [
							'UF_NAME' => $value,
							'UF_XML_ID' => $strXmlId,
						];
						$strClass = IBlock::getHighloadClass($strTableName);
						$obResult = $strClass::add($arItemFields);
						if($obResult->isSuccess()){
							$arSaveValue[] = $strXmlId;
						}
					}
				}
				$this->update($intSectionId, [$arProperty['FIELD_NAME'] => $arSaveValue]);
				break;
			case 'Money':
				$arSaveValue = [];
				foreach($arValues as $obValue){
					$strValue = sprintf('%s|%s', $obValue->getValue(), $obValue->get('CURRENCY'));
					$arSaveValue[] = $strValue;
				}
				$this->update($intSectionId, [$arProperty['FIELD_NAME'] => $arSaveValue]);
				break;
			case 'HTML':
				$arSaveValue = [];
				foreach($arValues as $obValue){
					$strHtmlType = 
					$arValue = [
						'VALUE' => [
							'TEXT' => $obValue->getValue(),
							'TYPE' => $this->getValueHtmlType($obValue->getValue(), $obValue),
						],
					];
					if(strlen($obValue->getDescription())){
						$arValue['DESCRIPTION'] = $obValue->getDescription();
					}
					$arSaveValue[] = $arValue;
				}
				$this->update($intSectionId, [$arProperty['FIELD_NAME'] => $arSaveValue]);
				break;
			default:
				$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y',
					$this->get('multiple_mode'));
				$this->update($intSectionId, [$arProperty['FIELD_NAME'] => $mSaveValue]);
				break;
		}
		return true;
	}
	protected function processSectionPropertyN($intSectionId, $arProperty, $arValues){
		$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y',
			$this->get('multiple_mode'));
		$this->update($intSectionId, [$arProperty['FIELD_NAME'] => $arSaveValue]);
		return true;
	}
	protected function processSectionPropertyL($intSectionId, $arProperty, $arValues){
		$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y',
			$this->get('multiple_mode'));
		$mSaveValue = is_array($mSaveValue) ? $mSaveValue : (strlen($mSaveValue) ? [$mSaveValue] : []);
		$arSaveValue = [];
		foreach($mSaveValue as $value){
			$arFilter = ['PROPERTY_ID' => $arProperty['ID'], 'VALUE' => $value];
			if($arEnum = \CIBlockPropertyEnum::getList([], $arFilter)->getNext()){
				$arSaveValue[] = $arEnum['ID'];
			}
			else{
				$arEnumFields = [
					'PROPERTY_ID' => $arProperty['ID'],
					'VALUE' => $value,
				];
				$obEnum = new \CIBlockPropertyEnum;
				if($intEnumId = $obEnum->add($arEnumFields)){
					$arSaveValue[] = $intEnumId;
				}
			}
		}
		$this->update($intSectionId, [$arProperty['FIELD_NAME'] => $arSaveValue]);
		return true;
	}
	protected function processSectionPropertyE($intSectionId, $arProperty, $arValues){
		$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y',
			$this->get('multiple_mode'));
		$mSaveValue = is_array($mSaveValue) ? $mSaveValue : (strlen($mSaveValue) ? [$mSaveValue] : []);
		$arSaveValue = [];
		# Check LINK_IBLOCK_ID
		if(!$arProperty['LINK_IBLOCK_ID']){
			$this->setError(static::getMessage('ERROR_NO_LINK_IBLOCK_ID', ['#PROPERTY_ID#' => $arProperty['ID']]));
			return false;
		}
		# Set
		foreach($mSaveValue as $value){
			$arFilter = ['IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], '=NAME' => $value];
			if($arSection = \CIBlockElement::getList([], $arFilter, false, ['nTopCount' => 1])->getNext()){
				$arSaveValue[] = $arElement['ID'];
			}
			else{
				$arElementFields = [
					'IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'],
					'NAME' => $value,
					'CODE' => \CUtil::translit($value, LANGUAGE_ID, [
						'max_len' => 255,
						'change_case' => 'L',
						'replace_space' => '_',
						'replace_other' => '_',
						'delete_repeat_replace' => true,
					]),
				];
				$obElement = new \CIBlockElement;
				if($intNewElementId = $obElement->add($arElementFields)){
					$arSaveValue[] = $intNewElementId;
				}
				else{
					$this->setError($obElement->LAST_ERROR);
					return false;
				}
			}
		}
		#
		$this->update($intSectionId, [$arProperty['FIELD_NAME'] => $arSaveValue]);
		return true;
	}
	protected function processSectionPropertyG($intSectionId, $arProperty, $arValues){
		$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y',
			$this->get('multiple_mode'));
		$mSaveValue = is_array($mSaveValue) ? $mSaveValue : (strlen($mSaveValue) ? [$mSaveValue] : []);
		$arSaveValue = [];
		# Check LINK_IBLOCK_ID
		if(!$arProperty['LINK_IBLOCK_ID']){
			$this->setError(static::getMessage('ERROR_NO_LINK_IBLOCK_ID', ['#PROPERTY_ID#' => $arProperty['ID']]));
			return false;
		}
		# Set
		foreach($mSaveValue as $value){
			$arFilter = ['IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], '=NAME' => $value];
			if($arSection = \CIBlockSection::getList([], $arFilter, false, ['nTopCount' => 1])->getNext()){
				$arSaveValue[] = $arElement['ID'];
			}
			else{
				$arElementFields = [
					'IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'],
					'NAME' => $value,
					'CODE' => \CUtil::translit($value, LANGUAGE_ID, [
						'max_len' => 255,
						'change_case' => 'L',
						'replace_space' => '_',
						'replace_other' => '_',
						'delete_repeat_replace' => true,
					]),
				];
				$obSection = new \CIBlockSection;
				if($intNewSectionId = $obSection->add($arElementFields)){
					$arSaveValue[] = $intNewSectionId;
				}
				else{
					$this->setError($obSection->LAST_ERROR);
					return false;
				}
			}
		}
		#
		$this->update($intSectionId, [$arProperty['FIELD_NAME'] => $arSaveValue]);
		return true;
	}
	protected function processSectionPropertyF($intSectionId, $arProperty, $arValues){
		$arSaveValue = [];
		foreach($arValues as $obValue){
			$arFile = $this->makeFileArray($intSectionId, $obValue->getValue());
			if(is_array($arFile)){
				$arFile['DESCRIPTION'] = $arFile['description'] = $obValue->getDescription();
				$arSaveValue[] = $arFile;
			}
		}
		if($arProperty['MULTIPLE'] != 'Y' && !empty($arSaveValue)){
			$arSaveValue = reset($arSaveValue);
		}
		$arOldFilesId = $this->getOldUserFieldImages($intSectionId, $arProperty['FIELD_NAME']);
		$this->update($intSectionId, [$arProperty['FIELD_NAME'] => $arSaveValue]);
		$this->removeOldUserFieldImages($arOldFilesId);
		return true;
	}
	
	/**
	 *	Get old user-field images
	 */
	protected function getOldUserFieldImages($intSectionId, $strField){
		$arResult = [];
		$arFilter = [
			'ID' => $intSectionId,
			'IBLOCK_ID' => $this->intIBlockId,
		];
		$arSelect = [
			'ID', 
			$strField,
		];
		$resSection = \CIBlockSection::getList([], $arFilter, false, $arSelect);
		if($arSection = $resSection->getNext(false, false)){
			$arResult = [];
			if(is_array($arSection[$strField])){
				foreach($arSection[$strField] as $intFileId){
					if(is_numeric($intFileId) && $intFileId > 0){
						$arResult[] = $intFileId;
					}
				}
			}
			elseif(is_numeric($arSection[$strField]) && $arSection[$strField] > 0){
				$arResult[] = $arSection[$strField];
			}
		}
		return $arResult;
	}
	
	/**
	 *	Remove old user-field images
	 */
	protected function removeOldUserFieldImages($arFiles){
		foreach($arFiles as $intFileId){
			\CFile::delete($intFileId);
		}
	}
		
	/**
	 *	Process seo-field
	 */
	protected function processSectionSeoField($intSectionId, $strSeoField, $arValues){
		$obValue = $this->cutMultipleValue($arValues, $this->get('multiple_mode'));
		$strSeoFieldFull = IBlock::$arSeoMapSection[$strSeoField];
		$arFields = [
			'IPROPERTY_TEMPLATES' => [
				$strSeoFieldFull => $obValue->getValue(),
			],
		];
		$bResult = $this->update($intSectionId, $arFields, true, true, false);
		return $bResult;
	}
	
	// *******************************************************************************************************************
	// Other methods
	// *******************************************************************************************************************
	
	/**
	 *	Get text format
	 */
	protected function getValueHtmlType($strValue, $obValue){
		$strType = false;
		$strHtmlType = toLower($this->get('html_type'));
		if($strHtmlType == 'auto') {
			if(is_object($obValue) && $obValue->get('HTML_TYPE')){
				if(in_array(toLower($obValue->get('HTML_TYPE')), ['text', 'html'])){
					$strType = toLower($obValue->get('HTML_TYPE'));
				}
			}
			elseif(is_string($strValue)){
				if(preg_match('#<[a-z]+[^>]*>#', $strValue)){
					$strType = 'html';
				}
			}
		}
		else{
			if(in_array($strHtmlType, ['text', 'html'])){
				$strType = $strHtmlType;
			}
		}
		return $strType;
	}
	
	/**
	 *	Make file array for $strValue
	 */
	protected function makeFileArray($intSectionId, $strValue){
		$mResult = false;
		if(is_numeric($strValue) && $strValue > 0){
			$mResult = \CFile::makeFileArray($strValue);
		}
		elseif(strlen($strValue)){
			if(is_file(Helper::root().$strValue)){
				$mResult = \CFile::makeFileArray(Helper::root().$strValue);
			}
			elseif(is_file($strValue)){
				$mResult = \CFile::makeFileArray($strValue);
			}
			elseif(preg_match('#^http[s]?://.*?$#', $strValue)){
				$mResult = \CFile::makeFileArray($strValue);
			}
		}
		if(is_array($mResult) && $this->get('file_convert_name') == 'Y'){
			$arSelect = ['NAME'];
			$arSection = IBlock::getSectionArray($intElementId, $this->intIBlockId, $arSelect);
			$strFilename = \CUtil::translit($arSection['~NAME'], LANGUAGE_ID, [
				'max_len' => 255,
				'change_case' => 'L',
				'replace_space' => '_',
				'replace_other' => '_',
				'delete_repeat_replace' => true,
			]);
			$strExtension = pathinfo($mResult['name'], PATHINFO_EXTENSION);
			$strFilename .= '.'.$strExtension;
			$mResult['name'] = $strFilename;
		}
		return $mResult;
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
	 *	Is skip null values
	 */
	protected function isSkipNullValues(){
		return $this->get(static::SKIP_NULL_VALUES) == 'Y';
	}
	
	/**
	 *	Format currency
	 *	Its very monstrously because we need to trim insignificant zeros in admin section
	 */
	protected function formatCurrency($fPrice, $strCurrency){
		if(\Bitrix\Main\Loader::includeModule('currency')){
			$mCallback = [__NAMESPACE__.'\CopyValuesCurrencyLang', 'CurrencyFormat'];
			return call_user_func($mCallback, $fPrice, $strCurrency, true);
		}
		else{
			return $fPrice.' '.$strCurrency;
		}
	}
	
	/**
	 *	Format URL
	 */
	protected function formatUrl($strRelativeUrl){
		$strDomain = $this->get('site_domain');
		$strDomain = preg_replace('#^(.*?)[/]*$#', '$1', $strDomain);
		return $strDomain.$strRelativeUrl;
	}
	
}

?>