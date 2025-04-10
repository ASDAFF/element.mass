<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginElement;

class PatternFill extends PluginElement {
	
	const MACRO_ID = '{=this.Id}';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE_FULL' => ['S', 'S:HTML', 'N'], 'IS_WRITEABLE' => 'Y'],
		'PROPERTIES' => ['TYPE_FULL' => ['S', 'S:HTML', 'N'], 'IS_WRITEABLE' => 'Y'],
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
		'SEO' => true,
		#
		'OFFERS.FIELDS' => ['TYPE_FULL' => ['S', 'S:HTML'], 'IS_WRITEABLE' => 'Y'],
		'OFFERS.PROPERTIES' => ['TYPE_FULL' => ['S', 'S:HTML'], 'IS_WRITEABLE' => 'Y'],
		'OFFERS.PRICES' => ['IS_WRITEABLE' => 'Y'],
		'OFFERS.SEO' => true,
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
	public function processElement($intElementId){
		$strField = $this->get('field');
		if(Helper::isEmpty($strField)){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		if($strOfferField = $this->isOfferField($strField)){
			$arOffers = $this->getElementOffers($intElementId);
			if(!empty($arOffers)){
				$intIBlockOffersId = $this->getOffersIBlockId();
				foreach($arOffers as $intOfferId){
					$strValue = $this->processPattern($intOfferId, $intIBlockOffersId);
					$bResult = $this->saveValues($intOfferId, $intIBlockOffersId, $strOfferField, $strValue);
				}
				$bResult = true;
			}
		}
		else{
			$strValue = $this->processPattern($intElementId);
			$bResult = $this->saveValues($intElementId, $this->intIBlockId, $strField, $strValue);
		}
		return $bResult;
	}
	
	/**
	 *	Process pattern
	 */
	protected function processPattern($intElementId, $intIBlockId=null){
		$intIBlockId = $intIBlockId ? $intIBlockId : $this->intIBlockId;
		$arSelect = ['IBLOCK_SECTION_ID', 'NAME', 'CODE', 'PREVIEW_TEXT', 'DETAIL_TEXT'];
		$arElement = IBlock::getElementArray($intElementId, $intIBlockId, ['FIELDS' => $arSelect]);
		$arFields = [
			'IBLOCK_ID' => $intIBlockId,
			'IBLOCK_SECTION_ID' => $arElement['IBLOCK_SECTION_ID'],
			'NAME' => $arElement['~NAME'],
			'CODE' => $arElement['~CODE'],
			'PREVIEW_TEXT' => $arElement['~PREVIEW_TEXT'],
			'DETAIL_TEXT' => $arElement['~DETAIL_TEXT'],
		];
		$strPattern = $this->get('pattern');
		$this->replaceId($intElementId, $strPattern);
		$obIPropTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates($intIBlockId, $intElementId);
		$obValues = $obIPropTemplates->getValuesEntity();
		$obEntity = $obValues->createTemplateEntity();
		$obEntity->setFields($arFields);
		$templates = $obIPropTemplates->findTemplates();
		$strResult = \Bitrix\Iblock\Template\Engine::process($obEntity, $strPattern);
		unset($obIPropTemplates, $obValues, $obEntity, $arFields, $arElement);
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
		$strResult = $this->priceRemoveWrongChars($strValue);
		if(\Bitrix\Main\Loader::includeModule('currency')){
			$strFormattedPriceOne = $this->priceRemoveWrongChars(Helper::currencyFormat(1, 'RUB'));
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
	 * 
	 */
	protected function priceRemoveWrongChars($strValue){
		return preg_replace('#&\#(\d+);#', '', $strValue);
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
	protected function replaceId($intElementId, &$strPattern){
		$strPattern = str_replace(static::MACRO_ID, $intElementId, $strPattern);
	}
	
	/**
	 *	Save values for element
	 */
	protected function saveValues($intElementId, $intIBlockId, $strField, $strValue){
		$bResult = false;
		if($this->isField($strField)){
			$arUpdateFields = [
				$strField => $strValue,
			];
			if(in_array($strField, ['PREVIEW_TEXT', 'DETAIL_TEXT'])){
				if($strTextType = $this->getTextType()){
					$arUpdateFields[$strField.'_TYPE'] = $strTextType;
				}
			}
			$bResult = $this->update($intElementId, $arUpdateFields);
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			$arProperty = IBlock::getPropertyById($intPropertyId, $intIBlockId);
			if($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['USER_TYPE'] == 'HTML'){
				$strValue = [
					'VALUE' => ['TYPE' => 'text', 'TEXT' => $strValue],
				];
				if($strTextType = $this->getTextType()){
					$strValue['VALUE']['TYPE'] = $strTextType;
				}
			}
			$this->setPropertyValue($intElementId, $intPropertyId, $strValue, null, $intIBlockId);
			$bResult = true;
		}
		elseif($intPriceId = $this->isPrice($strField)){
			$strCurrency = Helper::getBaseCurrency();
			return !!Helper::setProductPrice($intElementId, $intPriceId, $strValue, $strCurrency);
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			$strSeoFieldFull = IBlock::$arSeoMapElement[$strSeoField];
			$arFields = [
				'IPROPERTY_TEMPLATES' => [
					$strSeoFieldFull => $strValue,
				],
			];
			$bResult = $this->update($intElementId, $arFields, false, false, false);
		}
		return $bResult;
	}
	
	/**
	 *	Get html for <select>
	 */
	protected function getMacrosForSelect($intIBlockId=null){
		$strObjectName = 'window.wdaInheritedPropertiesTemplates';
		$strObjectFunc = 'insertIntoInheritedPropertiesTemplate';
		$intIBlockId = $intIBlockId ? $intIBlockId : $this->intIBlockId;
		$arMacros = \CIBlockParameters::GetInheritedPropertyTemplateElementMenuItems($intIBlockId,
			$strObjectName.'.'.$strObjectFunc, '', $this->getTextareaId());
		foreach($arMacros as $key1 => $arGroup){
			foreach($arGroup['MENU'] as $key2 => $arItem){
				break;
			}
			if(is_array($arItem)){
				$arItem['TEXT'] = 'ID';
				$arItem['ONCLICK'] = preg_replace('#({.*?})#', static::MACRO_ID, $arItem['ONCLICK'], 1);
				$arMacros[$key1]['MENU'] = array_merge([$arItem], $arGroup['MENU']);
				break;
			}
		}
		$strResult = '<option value="">---</option>';
		foreach($arMacros as $arGroup){
			if(!empty($arGroup['MENU'])){
				$strResult .= sprintf('<optgroup label="%s" data-code="%s">', htmlspecialcharsbx($arGroup['TEXT']), $strGroup);
					foreach($arGroup['MENU'] as $arItem){
						$strResult .= sprintf('<option value="%s">%s</option>', htmlspecialcharsbx($arItem['ONCLICK']),
							$arItem['TEXT']);
					}
				$strResult .= '</optgroup>';
			}
		}
		return $strResult;
	}
	
	/**
	 *	Get id for textarea
	 */
	protected function getTextareaId(){
		return 'pattern_'.$this->getId();
	}
	
	/**
	 *	AJAX: Load html macros for select
	 */
	protected function loadMacrosSelect(&$arJson){
		$strField = $this->arPost['field'];
		$intIBlockId = $this->intIBlockId;
		if($strOfferField = $this->isOfferField($strField)){
			$intIBlockId = $this->getOffersIBlockId();
		}
		return $this->getMacrosForSelect($intIBlockId);
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