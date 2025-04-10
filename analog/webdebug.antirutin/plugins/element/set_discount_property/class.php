<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class SetDiscountProperty extends PluginElement {
	
	protected $arFieldsFilter = [
		'PROPERTIES' => ['TYPE' => 'L'],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$bResult = false;
		$strField = $this->get('field');
		$arEnumsId = $this->get('value');
		$arEnumsId = is_array($arEnumsId) ? array_filter($arEnumsId) : $arEnumsId;
		if(!$this->isCatalogAvailable()){
			$this->setError(static::getMessage('ERROR_NO_CATALOG'));
			return false;
		}
		elseif(!strlen($strField)){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		if(empty($arEnumsId)){
			$this->setError(static::getMessage('ERROR_NO_VALUE'));
			return false;
		}
		$arEnumsId = is_array($arEnumsId) ? $arEnumsId : (strlen($arEnumsId) ? [$arEnumsId] : []);
		if(!empty($arEnumsId)){
			if($intPropertyId = $this->isProperty($strField)){
				$bDiscount = $this->productHasDiscount($intElementId);
				$arFeatures = [
					'PROPERTY_ID' => [$intPropertyId],
					'EMPTY_PROPERTIES' => true,
				];
				$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures, true);
				$mValue = $arElement['PROPERTIES'][$intPropertyId]['VALUE_ENUM_ID'];
				$mValue = is_array($mValue) ? $mValue : (strlen($mValue) && is_numeric($mValue) ? [$mValue] : []);
				if($bDiscount){
					$mValue = array_merge($mValue, $arEnumsId);
				}
				else{
					$mValue = array_diff($mValue, $arEnumsId);
				}
				$mValue = array_unique($mValue);
				if($mValue === []){
					$mValue = false;
				}
				$bResult = $this->setPropertyValue($intElementId, $intPropertyId, $mValue);
			}
			else{
				$this->setError('Wrong property');
			}
		}
		else{
			$this->setError('No value selected.');
		}
		return $bResult;
	}

	/**
	 * Check 'catalog' module is exists
	 */
	protected function isCatalogAvailable(){
		return \Bitrix\Main\Loader::includeModule('catalog');
	}

	/**
	 * Check main product has discount
	 */
	protected function productHasDiscount($intElementId){
		$bResult = $this->elementHasDiscount($intElementId);
		if(!$bResult && $this->get('use_offers') == 'Y'){
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, ['OFFERS' => true], true);
			if(is_array($arElement['OFFERS'])){
				foreach($arElement['OFFERS'] as $intOfferId){
					if($this->elementHasDiscount($intOfferId)){
						$bResult = true;
						break;
					}
				}

			}
		}
		return $bResult;
	}

	/**
	 * Check element or offer has discount
	 */
	protected function elementHasDiscount($intElementId){
		$bResult = false;
		if($arPrice = \CCatalogProduct::getOptimalPrice($intElementId, 1, [], 'N' , [], $this->get('site_id'))){
			$bResult = $arPrice['DISCOUNT_PRICE'] < $arPrice['RESULT_PRICE']['BASE_PRICE']
				|| !empty($arPrice['DISCOUNT']) || !empty($arPrice['DISCOUNT_LIST']);
		}
		return $bResult;
	}
	
	// *******************************************************************************************************************
	// Other methods
	// *******************************************************************************************************************
	
	/**
	 *	AJAX: Load field input
	 */
	protected function loadSelectValue(&$arJson){
		return $this->buildSelectOptions($this->arPost['field']);
	}
	
	/**
	 *	
	 */
	protected function buildSelectOptions($strField, $arSelectedValues=null){
		$strResult = '';
		$arSelectedValues = is_array($arSelectedValues) ? $arSelectedValues : [];
		if($intPropertyId = $this->isProperty($strField)){
			$strResult .= sprintf('<option value="" data-default="Y">%s</option>', static::getMessage('VALUE_DEFAULT'));
			foreach($this->getEnums($intPropertyId) as $arEnum){
				$strResult .= sprintf('<option value="%s"%s>%s</option>', $arEnum['ID'], 
					(in_array($arEnum['ID'], $arSelectedValues) ? ' selected="selected"' : ''), $arEnum['VALUE']);
			}
		}
		return $strResult;
	}
	
	/**
	 *	
	 */
	protected function getEnums($intPropertyId){
		$arResult = [];
		$arProperty = IBlock::getPropertyById($intPropertyId, $this->intIBlockId);
		if(is_array($arProperty)){
			$strType = $arProperty['PROPERTY_TYPE'].(strlen($arProperty['USER_TYPE']) ? ':'.$arProperty['USER_TYPE'] : '');
			switch($strType){
				case 'L':
					$arResult = IBlock::getEnumsL($intPropertyId, $this->intIBlockId);
					break;
			}
		}
		return $arResult;
	}
	
}

?>