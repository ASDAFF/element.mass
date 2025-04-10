<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginSection;

class ReplaceText extends PluginSection {
	
	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE_FULL' => ['S', 'S:HTML', 'N'], 'IS_WRITEABLE' => 'Y'],
		'PROPERTIES' => ['TYPE_FULL' => ['S', 'S:HTML', 'N'], 'IS_WRITEABLE' => 'Y'],
		'SEO' => true,
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		if($this->isEmpty('field')){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		$bResult = false;
		switch($this->get('mode')){
			case 'simple':
				$bResult = $this->replaceSimple($intSectionId);
				break;
			case 'reg_exp':
				$bResult = $this->replaceRegExp($intSectionId);
				break;
			case 'append':
				$bResult = $this->replaceAppend($intSectionId);
				break;
			case 'prepend':
				$bResult = $this->replacePrepend($intSectionId);
				break;
		}
		return $bResult;
	}
	
	/**
	 *	Get field/property values
	 */
	protected function getValues($intSectionId){
		$arResult = [];
		$strField = $this->get('field');
		if($this->isSectionField($strField)){
			$arSelect = [$strField];
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arSelect);
			$arResult[] = [
				'FIELD' => $strField,
				'VALUE' => $arSection[$strField],
			];
		}
		elseif($strProperty = $this->isSectionProperty($strField)){
			$arSelect = [$strField];
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arSelect);
			$mValue = $arSection['PROPERTIES'][$strProperty];
			$mValue = is_array($mValue) ? $mValue : (strlen($mValue) ? [$mValue] : []);
			foreach($mValue as $value){
				$arResult[] = [
					'FIELD' => $strField,
					'VALUE' => $value,
				];
			}
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			$arSelect = ['SEO_TEMPLATES'];
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arSelect, true);
			$arResult[] = [
				'FIELD' => $strField,
				'VALUE' => $arSection['SEO_TEMPLATES'][$strSeoField]['TEMPLATE'],
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
	protected function replaceSimple($intSectionId){
		$strSearch = $this->get('simple_search');
		$strReplace = $this->get('simple_replace');
		$bCaseSensitive = $this->get('simple_case_sensitive') == 'Y';
		if(!strlen($strSearch)){
			$this->setError(static::getMessage('ERROR_NO_SIMPLE_SEARCH'));
			return false;
		}
		$arValues = $this->getValues($intSectionId);
		foreach($arValues as $key => $obValue){
			$mValue = $obValue->getValue();
			$bHtmlValue = is_array($mValue) && count($mValue) == 2 && isset($mValue['TEXT']) && isset($mValue['TYPE']);
			if($bHtmlValue){
				if($bCaseSensitive){
					$mValue['TEXT'] = str_replace($strSearch, $strReplace, $mValue['TEXT']);
				}
				else{
					$mValue['TEXT'] = Helper::mb_str_ireplace($strSearch, $strReplace, $mValue['TEXT']);
				}
			}
			else{
				if($bCaseSensitive){
					$mValue = str_replace($strSearch, $strReplace, $mValue);
				}
				else{
					$mValue = Helper::mb_str_ireplace($strSearch, $strReplace, $mValue);
				}
			}
			$obValue->setValue($mValue);
		}
		$this->saveValues($intSectionId, $arValues);
		return true;
	}
	
	/**
	 *	
	 */
	protected function replaceRegExp($intSectionId){
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
		$arValues = $this->getValues($intSectionId);
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
		$this->saveValues($intSectionId, $arValues);
		return true;
	}
	
	/**
	 *	
	 */
	protected function replaceAppend($intSectionId){
		$strText = $this->get('append_text');
		if(!strlen($strText)){
			$this->setError(static::getMessage('ERROR_NO_APPEND_TEXT'));
			return false;
		}
		$arValues = $this->getValues($intSectionId);
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
		$this->saveValues($intSectionId, $arValues);
		return true;
	}
	
	/**
	 *	
	 */
	protected function replacePrepend($intSectionId){
		$strText = $this->get('prepend_text');
		if(!strlen($strText)){
			$this->setError(static::getMessage('ERROR_NO_PREPEND_TEXT'));
			return false;
		}
		$arValues = $this->getValues($intSectionId);
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
		$this->saveValues($intSectionId, $arValues);
		return true;
	}
	
	/**
	 *	Save values for element
	 */
	protected function saveValues($intSectionId, $arValues){
		$bResult = false;
		$strField = $this->get('field');
		if(!empty($arValues)){
			if($this->isSectionField($strField)){
				$obFirst = $this->cutMultipleValue($arValues, static::MULTIPLE_MODE_FIRST);
				if(is_object($obFirst)){
					$bResult = $this->update($intSectionId, [$strField => $obFirst->getValue()]);
				}
			}
			elseif($strProperty = $this->isSectionProperty($strField)){
				$bMultiple = $this->isPropertyMultiple($strProperty);
				$bDescription = $arProperty['WITH_DESCRIPTION'] == 'Y';
				$arSaveValues = $this->transformObjectToValue($arValues, $bMultiple, false, $bDescription);
				$bResult = $this->update($intSectionId, [$strProperty => $arSaveValues]);
			}
			elseif($strSeoField = $this->isSeoField($strField)){
				$obFirst = $this->cutMultipleValue($arValues, static::MULTIPLE_MODE_FIRST);
				if(is_object($obFirst)){
					$strSeoFieldFull = IBlock::$arSeoMapSection[$strSeoField];
					$this->setSeoField($intSectionId, $strSeoFieldFull, $obFirst->getValue());
				}
			}
		}
		return $bResult;
	}
	
}

?>