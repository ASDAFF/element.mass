<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginSection;

class PatternFill extends PluginSection {
	
	const MACRO_ID = '{=this.Id}';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE_FULL' => ['S', 'S:HTML'], 'IS_WRITEABLE' => 'Y'],
		'PROPERTIES' => ['TYPE_FULL' => ['S', 'S:HTML'], 'IS_WRITEABLE' => 'Y'],
	];
	
	protected $arJs = [
		'/bitrix/js/iblock/iblock_edit.js',
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
		$strValue = $this->processPattern($intSectionId);
		$bResult = $this->saveValues($intSectionId, $strValue);
		return $bResult;
	}
	
	/**
	 *	Process pattern
	 */
	protected function processPattern($intSectionId){
		$arSelect = ['IBLOCK_SECTION_ID', 'NAME', 'CODE', 'DESCRIPTION', 'UF_*'];
		$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arSelect);
		$arFields = [
			'IBLOCK_ID' => $this->intIBlockId,
			'IBLOCK_SECTION_ID' => $arSection['IBLOCK_SECTION_ID'],
			'NAME' => $arSection['~NAME'],
			'CODE' => $arSection['~CODE'],
			'DESCRIPTION' => $arSection['~DESCRIPTION'],
		];
		if(is_array($arSection['PROPERTIES'])){
			foreach($arSection['PROPERTIES'] as $strField => $strValue){
				if($this->isSectionProperty($strField)){
					$arFields[$strField] = $arSection['PROPERTIES']['~'.$strField];
				}
			}
		}
		$strPattern = $this->get('pattern');
		$this->replaceId($intSectionId, $strPattern);
		$obIPropTemplates = new \Bitrix\Iblock\InheritedProperty\SectionTemplates($this->intIBlockId, $intSectionId);
		$obValues = $obIPropTemplates->getValuesEntity();
		$obEntity = $obValues->createTemplateEntity();
		$obEntity->setFields($arFields);
		$templates = $obIPropTemplates->findTemplates();
		$strResult = \Bitrix\Iblock\Template\Engine::process($obEntity, $strPattern);
		unset($obIPropTemplates, $obValues, $obEntity, $arFields, $arSection);
		if($this->get('use_as_formula') == 'Y'){
			$strResult = $this->calcFormula($strResult);
		}
		return $strResult;
	}
	
	/**
	 *	Calculate formula
	 */
	protected function calcFormula($strValue){
		$strValue = $this->stripPrices($strValue);
		$strResult = null;
		$strEval = 'return ('.$strValue.');';
		try{
			$strResult = eval($strEval);
		}
		catch(\Error $obError){
			$this->setError($obError->getMessage());
			$this->debug($obError->getMessage());
			$this->debug('Expression: '.$strEval);
		}
		return $strResult;
	}
	
	/**
	 *	
	 */
	protected function stripPrices($strValue){
		$strResult = $strValue;
		if(\Bitrix\Main\Loader::includeModule('currency')){
			$strFormattedPriceOne = Helper::currencyFormat(1, 'RUB');
			$strPattern = '[0-9][0-9.,&nbsp;\s]*';
			$strPricePregPattern = preg_replace('#('.$strPattern.')#', $strPattern, $strFormattedPriceOne);
			$strResult = preg_replace_callback('#('.$strPricePregPattern.')#', function($arMatch){
				$strResult =  preg_replace('#[^\\d.,]+#', '', $arMatch[0]);
				$strResult = $this->removeRepetitiveDecimalPoint($strResult);
				return $strResult;
			}, $strResult);
			$strResult = $this->convertDecimalPoint($strResult);
		}
		return $strResult;
	}

	/**
	 *	Convert '10.075' and '10,075' to '10.075' (or '10,075' considering of locale settings)
	 */
	protected function convertDecimalPoint($strFloatValue){
		$arReplace = ['.' => ','];
		$arLocale = localeConv();
		$strPoint = $arLocale['decimal_point'];
		if($strPoint == '.'){
			$arReplace = array_flip($arReplace);
		}
		return str_replace(array_keys($arReplace), array_values($arReplace), $strFloatValue);
	}
	
	/**
	 *	
	 */
	protected function removeRepetitiveDecimalPoint($strValue){
		$strResult = '';
		$bDecPointPassed = false;
		for($i=0; $i < strlen($strValue); $i++){
			$strChar = substr($strValue, $i, 1);
			$bPoint = in_array($strChar, ['.', ',']);
			if(!$bPoint || !$bDecPointPassed){
				$strResult .= $strChar;
			}
			if($bPoint){
				$bDecPointPassed = true;
			}
		}
		return $strResult;
	}
	
	/**
	 *	Replace ID macro
	 */
	protected function replaceId($intSectionId, &$strPattern){
		$strPattern = str_replace(static::MACRO_ID, $intSectionId, $strPattern);
	}
	
	/**
	 *	Save values for Section
	 */
	protected function saveValues($intSectionId, $strValue){
		$bResult = false;
		$strField = $this->get('field');
		if($this->isSectionField($strField)){
			$arUpdateFields = [
				$strField => $strValue,
			];
			if(in_array($strField, ['DESCRIPTION'])){
				if($strTextType = $this->getTextType()){
					$arUpdateFields[$strField.'_TYPE'] = $strTextType;
				}
			}
			$bResult = $this->update($intSectionId, $arUpdateFields);
		}
		elseif($intPropertyId = $this->isSectionProperty($strField)){
			$bResult = $this->update($intSectionId, [$strField => $strValue]);
		}
		return $bResult;
	}

	/**
	 * Get text type
	 */
	protected function getTextType($strDefault=null){
		$strResult = null;
		$arAllowedTypes = ['text', 'html'];
		if(is_string($strDefault) && in_array($strDefault, $arAllowedTypes)){
			$strResult = $strDefault;
		}
		if(strlen($strTextType = $this->get('text_type'))){
			if(in_array($strTextType, $arAllowedTypes)){
				$strResult = $strTextType;
			}
		}
		return $strResult;
	}
	
}

?>