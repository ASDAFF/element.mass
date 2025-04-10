<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginElement;

class ReplaceText extends PluginElement {
	
	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE_FULL' => ['S', 'S:HTML', 'N'], 'IS_WRITEABLE' => 'Y'],
		'PROPERTIES' => ['TYPE' => ['S', 'N'], 'IS_WRITEABLE' => 'Y'],
		'SEO' => true,
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if($this->isEmpty('field')){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		$bResult = false;
		switch($this->get('mode')){
			case 'simple':
				$bResult = $this->replaceSimple($intElementId);
				break;
			case 'multiline':
				$bResult = $this->replaceMultiline($intElementId);
				break;
			case 'reg_exp':
				$bResult = $this->replaceRegExp($intElementId);
				break;
			case 'append':
				$bResult = $this->replaceAppend($intElementId);
				break;
			case 'prepend':
				$bResult = $this->replacePrepend($intElementId);
				break;
		}
		return $bResult;
	}
	
	/**
	 *	Get field/property values
	 */
	protected function getValues($intElementId){
		$arResult = [];
		$strField = $this->get('field');
		if($this->isField($strField)){
			$arFeatures = ['FIELDS' => [$strField]];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures, true);
			$arResult[] = [
				'FIELD' => $strField,
				'VALUE' => $arElement['~'.$strField],
			];
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			$arFeatures = ['PROPERTY_ID' => [$intPropertyId], 'EMPTY_PROPERTIES' => true];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures, true);
			$arProperty = $arElement['PROPERTIES'][$intPropertyId];
			if($arProperty['MULTIPLE'] == 'Y' && is_array($arProperty['~VALUE'])){
				foreach($arProperty['~VALUE'] as $key => $value){
					$arResult[] = [
						'FIELD' => $strField,
						'FIELD_ARRAY' => $arProperty,
						'VALUE' => $value,
						'DESCRIPTION' => $arProperty['DESCRIPTION'][$key],
					];
				}
			}
			else{
				$arResult[] = [
					'FIELD' => $strField,
					'FIELD_ARRAY' => $arProperty,
					'VALUE' => $arProperty['~VALUE'],
					'DESCRIPTION' => $arProperty['DESCRIPTION'],
				];
			}
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			$arFeatures = ['SEO_TEMPLATES' => true];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures, true);
			$arResult[] = [
				'FIELD' => $strField,
				'VALUE' => $arElement['SEO_TEMPLATES'][$strSeoField]['TEMPLATE'],
			];
		}
		foreach($arResult as $key => $arValue){
			$arResult[$key] = new ValueItem($arValue);
		}
		return $arResult;
	}
	
	/**
	 *	
	 */
	protected function replaceSimple($intElementId){
		$strSearch = $this->get('simple_search');
		$strReplace = $this->get('simple_replace');
		$bCaseSensitive = $this->get('simple_case_sensitive') == 'Y';
		if(!strlen($strSearch)){
			$this->setError(static::getMessage('ERROR_NO_SIMPLE_SEARCH'));
			return false;
		}
		$arValues = $this->getValues($intElementId);
		foreach($arValues as $key => $obValue){
			$mValue = $obValue->getValue();
			$bHtmlValue = is_array($mValue) && count($mValue) == 2 && isset($mValue['TEXT']) && isset($mValue['TYPE']);
			#
			if($bHtmlValue){
				$mValueText = &$mValue['TEXT'];
			}
			else{
				$mValueText = &$mValue;
			}
			#
			$this->doSimpleReplace($mValueText, $strSearch, $strReplace, $bCaseSensitive);
			$obValue->setValue($mValue);
		}
		$this->saveValues($intElementId, $arValues);
		return true;
	}
	
	/**
	 *	
	 */
	protected function replaceMultiline($intElementId){
		$strSearch = $this->get('multiline_search');
		$strReplace = $this->get('multiline_replace');
		$bCaseSensitive = $this->get('multiline_case_sensitive') == 'Y';
		if(!strlen($strSearch)){
			$this->setError(static::getMessage('ERROR_NO_MULTILINE_SEARCH'));
			return false;
		}
		$arSearch = array_map(function($strItem){
			return trim($strItem, "\r");
		}, explode("\n", $strSearch));
		$arValues = $this->getValues($intElementId);
		foreach($arValues as $key => $obValue){
			$mValue = $obValue->getValue();
			$bHtmlValue = is_array($mValue) && count($mValue) == 2 && isset($mValue['TEXT']) && isset($mValue['TYPE']);
			#
			if($bHtmlValue){
				$mValueText = &$mValue['TEXT'];
			}
			else{
				$mValueText = &$mValue;
			}
			#
			foreach($arSearch as $strSearchItem){
				$this->doSimpleReplace($mValueText, $strSearchItem, $strReplace, $bCaseSensitive);
			}
			$obValue->setValue($mValue);
		}
		$this->saveValues($intElementId, $arValues);
		return true;
	}

	/**
	 * Replace function
	 */
	protected function doSimpleReplace(&$strText, $strSearch, $strReplace, $bCaseSensitive){
		if($bCaseSensitive){
			$strText = str_replace($strSearch, $strReplace, $strText);
		}
		else{
			$strText = Helper::mb_str_ireplace($strSearch, $strReplace, $strText);
		}
	}
	
	/**
	 *	
	 */
	protected function replaceRegExp($intElementId){
		$strSearch = $this->get('reg_exp_search');
		$strReplace = $this->get('reg_exp_replace');
		$intLimit = intVal($this->get('reg_exp_limit'));
		if(!is_numeric($intLimit) || $intLimit <= 0){
			$intLimit = -1;
		}
		if(!strlen($strSearch)){
			$this->setError(static::getMessage('ERROR_NO_REG_EXP_SEARCH'));
			return false;
		}
		$arValues = $this->getValues($intElementId);
		foreach($arValues as $key => $obValue){
			$mValue = $obValue->getValue();
			$bHtmlValue = is_array($mValue) && count($mValue) == 2 && isset($mValue['TEXT']) && isset($mValue['TYPE']);
			if($bHtmlValue){
				$mValue['TEXT'] = preg_replace($strSearch, $strReplace, $mValue['TEXT'], $intLimit);
			}
			else{
				$mValue = preg_replace($strSearch, $strReplace, $mValue, $intLimit);
			}
			$obValue->setValue($mValue);
		}
		$this->saveValues($intElementId, $arValues);
		return true;
	}
	
	/**
	 *	
	 */
	protected function replaceAppend($intElementId){
		$strText = $this->get('append_text');
		if(!strlen($strText)){
			$this->setError(static::getMessage('ERROR_NO_APPEND_TEXT'));
			return false;
		}
		$arValues = $this->getValues($intElementId);
		foreach($arValues as $key => $obValue){
			$mValue = $obValue->getValue();
			$bHtmlValue = is_array($mValue) && count($mValue) == 2 && isset($mValue['TEXT']) && isset($mValue['TYPE']);
			if($bHtmlValue){
				$mValue['TEXT'] = $mValue['TEXT'].$strText;
			}
			else{
				$mValue = $mValue.$strText;
			}
			$obValue->setValue($mValue);
		}
		$this->saveValues($intElementId, $arValues);
		return true;
	}
	
	/**
	 *	
	 */
	protected function replacePrepend($intElementId){
		$strText = $this->get('prepend_text');
		if(!strlen($strText)){
			$this->setError(static::getMessage('ERROR_NO_PREPEND_TEXT'));
			return false;
		}
		$arValues = $this->getValues($intElementId);
		foreach($arValues as $key => $obValue){
			$mValue = $obValue->getValue();
			$bHtmlValue = is_array($mValue) && count($mValue) == 2 && isset($mValue['TEXT']) && isset($mValue['TYPE']);
			if($bHtmlValue){
				$mValue['TEXT'] = $strText.$mValue['TEXT'];
			}
			else{
				$mValue = $strText.$mValue;
			}
			$obValue->setValue($mValue);
		}
		$this->saveValues($intElementId, $arValues);
		return true;
	}
	
	/**
	 *	Save values for element
	 */
	protected function saveValues($intElementId, $arValues){
		$bResult = false;
		$strField = $this->get('field');
		if(!empty($arValues)){
			if($this->isField($strField)){
				$obFirst = $this->cutMultipleValue($arValues, static::MULTIPLE_MODE_FIRST);
				if(is_object($obFirst)){
					$bResult = $this->update($intElementId, [$strField => $obFirst->getValue()]);
				}
			}
			elseif($intPropertyId = $this->isProperty($strField)){
				$obFirst = $this->cutMultipleValue($arValues, static::MULTIPLE_MODE_FIRST);
				if(is_object($obFirst)){
					$arProperty = $obFirst->getFieldArray();
					$arValues = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y', false,
						$arProperty['WITH_DESCRIPTION'] == 'Y');
					$this->checkAndConvertHtmlValue($arValues, $intPropertyId);
					$this->setPropertyValue($intElementId, $intPropertyId, $arValues);
					$bResult = true;
				}
			}
			elseif($strSeoField = $this->isSeoField($strField)){
				$obFirst = $this->cutMultipleValue($arValues, static::MULTIPLE_MODE_FIRST);
				if(is_object($obFirst)){
					$strSeoFieldFull = IBlock::$arSeoMapElement[$strSeoField];
					$this->setSeoField($intElementId, $strSeoFieldFull, $obFirst->getValue());
				}
			}
		}
		return $bResult;
	}

	/**
	 * Correct value for save to S:HTML
	 */
	protected function checkAndConvertHtmlValue(&$arValues, $intPropertyId){
		if(is_array($arValues) && isset($arValues['TYPE']) && isset($arValues['TEXT'])){
			$arProperty = IBlock::getPropertyById($intPropertyId, $this->intIBlockId);
			if($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['USER_TYPE'] == 'HTML'){
				$arValues = [
					'VALUE' => [
						'TYPE' => toLower($arValues['TYPE']),
						'TEXT' => $arValues['TEXT'],
					],
				];
			}
		}
	}
	
}

?>