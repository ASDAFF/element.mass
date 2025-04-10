<?php
namespace WD\Antirutin\Plugins\Element;

use
\WD\Antirutin\Helper,
\WD\Antirutin\IBlock,
\WD\Antirutin\ValueItem,
\WD\Antirutin\PluginElement;

class CopyOfferImages extends PluginElement {
	
	const GROUP = 'IMAGES';

	protected $arFieldsFilter = [
		'OFFERS.FIELDS' => ['TYPE_FULL' => ['F']],
		'OFFERS.PROPERTIES' => ['TYPE_FULL' => ['F']],
	];
	protected $arFieldsFilter2 = [
		'PROPERTIES' => ['IS_WRITEABLE' => 'Y', 'TYPE_FULL' => ['F']],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************

	/**
	 *	Execute!
	 */

	public function processElement($intElementId){
		if($this->isEmpty('source')){
			$this->setError(static::getMessage('ERROR_NO_SOURCE_FIELD'));
			return false;
		}
		if($this->isEmpty('target')){
			$this->setError(static::getMessage('ERROR_NO_TARGET_FIELD'));
			return false;
		}
		#
		$bResult = $this->processElementOP($intElementId);
		return $bResult;
	}

	/**
	 *	Copy values offer-to-product
	 */
	protected function processElementOP($intElementId){
		$arOffers = $this->getElementOffers($intElementId);
		$arAllValues = [];
		foreach($arOffers as $intOfferId){
			$arValues = $this->getFieldValue($intOfferId, $this->getOffersIBlockId());
			$arAllValues = array_merge($arAllValues, $arValues);
		}
		$this->processField($intElementId, $arAllValues);
		return true;
	}

	protected function getFieldValue($intElementId, $intIBlockId=null){
		$arResult = [];
		$strField = $this->get('source');
		if($strOfferField = $this->isOfferField($strField)){
			$strField = $strOfferField;
		}
		$arField = [];
		$intIBlockId = $intIBlockId ? $intIBlockId : $this->intIBlockId;
		if($this->isField($strField)){
			$arFeatures = [
				'FIELDS' => [$strField],
			];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
			if(is_array($arElement[$strField])){
				$arFakeProp = [
					'PROPERTY_TYPE' => 'F',
					'VALUE' => [$arElement[$strField]['ID']],
				];
				$arResult = $this->formatValueProperty($arFakeProp);
			}
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			$arFeatures = [
				'PROPERTY_ID' => [$intPropertyId],
				'EMPTY_PROPERTIES' => true,
			];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
			$arProperty = $arElement['PROPERTIES'][$intPropertyId];
			$arProperty = array_merge($arElement['PROPERTIES'][$intPropertyId], [
				'ELEMENT_ID' => $intElementId,
				'TYPE_FULL' => $arProperty['PROPERTY_TYPE'].
					(strlen($arProperty['USER_TYPE']) ? ':'.$arProperty['USER_TYPE'] : ''),
			]);
			$arField = $arProperty;
			$arResult = $this->formatValueProperty($arProperty);
		}
		# Set empty value if source value not found
		if(empty($arResult)){
			$arResult[] = new ValueItem([]);
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
					'DESCRIPTION' => $arProperty['VALUE_DESCRIPTION'][$key],
				];
				$arResultTmp[] = new ValueItem($arValue);
			}
		}
		return $arResultTmp;
	}

	protected function processField($intElementId, $arValues, $intIBlockId=null){
		$strField = $this->get('target');
		$intIBlockId = $intIBlockId ? $intIBlockId : $this->intIBlockId;
		$intPropertyId = $this->isProperty($strField);
		return $this->processElementProperty($intElementId, $intIBlockId, $intPropertyId, $arValues);
	}

	protected function formatValueProperty(&$arProperty){
		$arResult = [];
		#
		$arValue = $arProperty['VALUE'];
		#
		$arValue = is_array($arValue) ? $arValue : (!is_null($arValue) && strlen($arValue) ? [$arValue] : []);
		$arProperty['VALUE_ARRAY'] = $arValue;
		#
		$arDescr = $arProperty['DESCRIPTION'];
		$arDescr = is_array($arDescr) ? $arDescr : (!is_null($arDescr) && strlen($arDescr) ? [$arDescr] : []);
		$arProperty['VALUE_DESCRIPTION'] =  $arDescr;
		#
		if($arProperty['PROPERTY_TYPE'] == 'F'){
			$arResult = $this->formatValue_F($arProperty);
		}
		return $arResult;
	}

	protected function formatValue_F($arProperty){
		$arResult = [];
		if(!empty($arProperty['VALUE_ARRAY'])){
			if(!is_array($arProperty['VALUE_DESCRIPTION'])){
				$arProperty['VALUE_DESCRIPTION'] = [];
			}
		}
		foreach($arProperty['VALUE_ARRAY'] as $key => $value){
			$arResult[] = new ValueItem([
				'FIELD' => $strField,
				'FIELD_ARRAY' => $arProperty,
				'VALUE' => $value,
				'DESCRIPTION' => $arProperty['VALUE_DESCRIPTION'][$key],
			]);
		}
		return $arResult;
	}

	protected function processElementProperty($intElementId, $intIBlockId, $intPropertyId, $arSourceValues){
		$arDestinationProperty = $this->getProperty($intPropertyId, true, $intIBlockId);
		return $this->processElementPropertyF($intElementId, $intIBlockId, $arDestinationProperty, $arSourceValues);
	}

	protected function processElementPropertyF($intElementId, $intIBlockId, $arDestinationProperty, $arSourceValues){
		$arSaveValue = [];
		if($arSourceValues['MULTIPLE'] == 'Y'){
			$obValue = $this->cutMultipleValue($arSourceValues);
			if($obValue){
				$arSourceValues[] = $obValue;
			}
		}
		foreach($arSourceValues as $obValue){
			if($intFileId = $obValue->getValue()){
				if($arFile = \CFile::makeFileArray($intFileId)){
					$arFile['DESCRIPTION'] = $arFile['description'] = $obValue->getDescription();
					$arSaveValue[] = $arFile;
				}
			}
		}
		$this->setPropertyValue($intElementId, $arDestinationProperty['ID'], $arSaveValue, null, $intIBlockId);
		return true;
	}

}
