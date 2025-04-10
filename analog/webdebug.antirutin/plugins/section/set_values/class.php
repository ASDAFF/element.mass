<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginSection;

class SetValues extends PluginSection {
	
	const SAVE_CURRENT_VALUES = 'save_current_values';
	const REMOVE_DUPLICATES = 'remove_duplicates';
	const REMOVE_FILES_IF_EMPTY_VALUE = 'remove_files_if_empty_value';
	
	const INPUT_VALUE = 'value';
	const INPUT_DESCRIPTION = 'value_description';
	
	const PHP_DEBUG_MODE_RESULT = '#PHP_DEBUG_MODE_RESULT#';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['IS_WRITEABLE' => 'Y', '!CODE' => ['IBLOCK_SECTION_ID', 'DATE_CREATE', 'CREATED_BY']],
		'PROPERTIES' => ['IS_WRITEABLE' => 'Y'],
		'SEO' => ['IS_WRITEABLE' => 'Y'],
		'CATALOG' => ['IS_WRITEABLE' => 'Y'],
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		if(!strlen($this->get('field'))){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		$strField = $this->get('field');
		$mValue = $this->getValueForSave($intSectionId, $strField, true);
		if($mValue === static::PHP_DEBUG_MODE_RESULT){
			$bResult = true;
		}
		else{
			$bResult = $this->processField($intSectionId, $this->get('field'), $mValue, $this->get('description'));
		}
		return $bResult;
	}
	
	/**
	 *	Process single field
	 */
	protected function processField($intSectionId, $strField, $mValue, $mDescription){
		if($this->isSectionField($strField)){
			return $this->processSectionField($intSectionId, $strField, $mValue, $mDescription);
		}
		elseif($strPropertyCode = $this->isSectionProperty($strField)){
			return $this->processSectionProperty($intSectionId, $strPropertyCode, $mValue, $mDescription);
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			return $this->processSectionSeoField($intSectionId, $strSeoField, $mValue);
		}
		$this->log(static::getMessage('LOG_FIELD_UNKNOWN', ['#FIELD#' => $strField]));
		return false;
	}
	
	/**
	 *	Process field
	 */
	protected function processSectionField($intSectionId, $strField, $mValue, $mDescription=null){
		switch($strField){
			case 'NAME':
				$bResult = $this->update($intSectionId, ['NAME' => $mValue]);
				break;
			case 'ACTIVE':
				$bResult = $this->update($intSectionId, ['ACTIVE' => ($mValue == 'Y' ? 'Y' : 'N')]);
				break;
			case 'SORT':
				$bResult = $this->update($intSectionId, ['SORT' => $mValue]);
				break;
			case 'DESCRIPTION':
				$arFields = [
					'DESCRIPTION' => $mValue
				];
				if(in_array($this->get('type'), ['text', 'html'])){
					$arFields['DESCRIPTION_TYPE'] = $this->get('type');
				}
				$bResult = $this->update($intSectionId, $arFields);
				break;
			case 'DESCRIPTION_TYPE':
				$bResult = $this->update($intSectionId, [$strField => $mValue]);
				break;
			case 'PICTURE':
			case 'DETAIL_PICTURE':
				$mValue = is_array($mValue) ? reset($mValue) : $mValue;
				if(strlen($mValue) && is_file(Helper::root().$mValue)){
					$mValue = \CFile::makeFileArray(Helper::root().$mValue);
					if(is_array($mValue) && $mValue['size'] > 0){
						$mDescription = is_array($mDescription) ? implode(' ', $mDescription) : $mDescription;
						if(strlen($mDescription)){
							$mValue['description'] = $mDescription;
							$this->replaceMacrosInFileDescription($mValue['description'], $intSectionId);
						}
						$bResult = $this->update($intSectionId, [$strField => $mValue]);
					}
				}
				elseif($this->isRemoveFilesIfEmptyValue() == 'Y'){
					$bResult = $this->update($intSectionId, [$strField => ['del' => 'Y']]);
				}
				break;
			case 'CODE':
				$bResult = $this->update($intSectionId, ['CODE' => $mValue]);
				break;
			case 'EXTERNAL_ID':
			case 'XML_ID':
				$bResult = $this->update($intSectionId, ['EXTERNAL_ID' => $mValue]);
				break;
		}
		return $bResult;
	}
	
	/**
	 *	Process property
	 */
	protected function processSectionProperty($intSectionId, $strPropertyCode, $mValue, $mDescription=null){
		$arProperty = $this->getProperty($strPropertyCode);
		if(!is_array($arProperty)){
			$this->log(static::getMessage('LOG_PROPERTY_NOT_FOUND', ['#IBLOCK_ID#' => $this->intIBlockId, 
				'#FIELD_NAME#' => $strPropertyCode]));
			return false;
		}
		if($arProperty['MULTIPLE'] == 'Y' && !is_array($mValue)){
			$mValue = [$mValue];
		}
		switch($arProperty['PROPERTY_TYPE']){
			case 'S':
				return $this->processSectionPropertyS($intSectionId, $arProperty, $mValue, $mDescription);
				break;
			case 'N':
				return $this->processSectionPropertyN($intSectionId, $arProperty, $mValue, $mDescription);
				break;
			case 'L':
				return $this->processSectionPropertyL($intSectionId, $arProperty, $mValue, $mDescription);
				break;
			case 'E':
				return $this->processSectionPropertyE($intSectionId, $arProperty, $mValue, $mDescription);
				break;
			case 'G':
				return $this->processSectionPropertyG($intSectionId, $arProperty, $mValue, $mDescription);
				break;
			case 'F':
				return $this->processSectionPropertyF($intSectionId, $arProperty, $mValue, $mDescription);
				break;
		}
	}
	protected function processSectionPropertyS($intSectionId, $arProperty, $mValue, $mDescription=null){
		$this->mergeValuesWithDescriptions($arProperty, $mValue, $mDescription);
		switch($arProperty['USER_TYPE']){
			default:
				$bResult = $this->update($intSectionId, [$arProperty['FIELD_NAME'] => $mValue]);
				break;
		}
		return $bResult;
	}
	protected function processSectionPropertyN($intSectionId, $arProperty, $mValue, $mDescription=null){
		$this->mergeValuesWithDescriptions($arProperty, $mValue, $mDescription);
		return $this->update($intSectionId, [$arProperty['FIELD_NAME'] => $mValue]);
	}
	protected function processSectionPropertyL($intSectionId, $arProperty, $mValue, $mDescription=null){
		return $this->update($intSectionId, [$arProperty['FIELD_NAME'] => $mValue]);
	}
	protected function processSectionPropertyE($intSectionId, $arProperty, $mValue, $mDescription=null){
		return $this->update($intSectionId, [$arProperty['FIELD_NAME'] => $mValue]);
	}
	protected function processSectionPropertyG($intSectionId, $arProperty, $mValue, $mDescription=null){
		return $this->update($intSectionId, [$arProperty['FIELD_NAME'] => $mValue]);
	}
	protected function processSectionPropertyF($intSectionId, $arProperty, $mValue, $mDescription=null){
		$mValue = is_array($mValue) ? $mValue : [$mValue];
		$mDescription = is_array($mDescription) ? $mDescription : [$mDescription];
		$mValueTmp = [];
		foreach($mValue as $intValueKey => $strValue){
			if(strlen($strValue) && is_file(Helper::root().$strValue)){
				$arFile = \CFile::makeFileArray(Helper::root().$strValue);
				if(is_array($arFile)){
					if(strlen($mDescription[$intValueKey])){
						$arFile['description'] = $mDescription[$intValueKey];
						$this->replaceMacrosInFileDescription($arFile['description'], $intSectionId);
					}
					if($arProperty['MULTIPLE'] == 'Y'){
						$mValueTmp[] = $arFile;
					}
					else{
						$mValueTmp = $arFile;
						break;
					}
				}
			}
		}
		if(!empty($mValueTmp) || $this->isRemoveFilesIfEmptyValue()){
			$this->removeOldUserFieldImages($intSectionId, $arProperty['FIELD_NAME']);
			return $this->update($intSectionId, [$arProperty['FIELD_NAME'] => $mValueTmp]);
		}
		return true;
	}
	
	/**
	 *	Remove old user-field images
	 */
	protected function removeOldUserFieldImages($intSectionId, $strField){
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
			$arOldFilesId = [];
			if(is_array($arSection[$strField])){
				foreach($arSection[$strField] as $intFileId){
					if(is_numeric($intFileId) && $intFileId > 0){
						$arOldFilesId[] = $intFileId;
					}
				}
			}
			elseif(is_numeric($arSection[$strField]) && $arSection[$strField] > 0){
				$arOldFilesId[] = $arSection[$strField];
			}
			foreach($arOldFilesId as $intFileId){
				\CFile::delete($intFileId);
			}
			return true;
		}
		return false;
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
	protected function processSectionSeoField($intSectionId, $strSeoField, $mValue){
		$strSeoFieldFull = IBlock::$arSeoMapSection[$strSeoField];
		$arFields = [
			'IPROPERTY_TEMPLATES' => [
				$strSeoFieldFull => $mValue,
			],
		];
		$bResult = $this->update($intSectionId, $arFields, false, false, false);
		return $bResult;
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
		$arField = $this->getFields($this->arFieldsFilter, $strField);
		if(!is_array($arField) || empty($arField)){
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
			$intColCount = is_numeric($arField['DATA']['SETTINGS']['SIZE']) ? $arField['DATA']['SETTINGS']['SIZE'] : 50;
			$intRowCount = is_numeric($arField['DATA']['SETTINGS']['ROWS']) ? $arField['DATA']['SETTINGS']['ROWS'] : 5;
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
			$bCanSaveCurrentValues = false;
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
		return $strFieldHtml;
	}
	
	/**
	 *	Get save value
	 */
	protected function getValueForSave($intSectionId, $strField, $bOffer){
		$mValue = null;
		if($this->get('use_custom_php') == 'Y'){
			$strCode = $this->get('php_code');
			if(strlen($strCode)){
				$intIBlockId = $this->intIBlockId;
				$strCode = 'namespace WD\Antirutin;'.PHP_EOL.$strCode.';';
				$arSection = IBlock::getSectionArray($intSectionId, $intIBlockId);
				$mValue = eval($strCode);
				if($this->get('php_debug_mode') == 'Y'){
					$this->debug($mValue);
					$mValue = static::PHP_DEBUG_MODE_RESULT;
				}
			}
		}
		else{
			$mValue = $this->get('value');
		}
		return $mValue;
	}
	
	/**
	 *	Get demo code
	 */
	protected  function getDemoCode(){
		return static::getMessage('DEMO_CODE');
	}

	/**
	 * 
	 */
	protected function replaceMacrosInFileDescription(&$strDescription, $intSectionId){
		if(strpos($strDescription, '#NAME#') !== false){
			$strName = '';
			$arQuery = [
				'filter' => ['ID' => $intSectionId],
				'select' => ['NAME'],
			];
			if($arSection = \Bitrix\IBlock\SectionTable::getList($arQuery)->fetch()){
				$strName = $arSection['NAME'];
			}
			$strDescription = str_replace('#NAME#', $strName, $strDescription);
		}
	}
	
}

?>