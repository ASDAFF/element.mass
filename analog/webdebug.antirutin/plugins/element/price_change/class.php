<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginElement;

class PriceChange extends PluginElement {
	
	const GROUP = 'CATALOG';
	
	protected $arFieldsFilter = [
		'PROPERTIES' => ['TYPE_FULL' => ['N', 'S', 'S:Money']],
		'CATALOG' => ['CODE' => 'PURCHASING_PRICE'],
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if($this->isEmpty('source')){
			$this->setError(static::getMessage('ERROR_NO_SOURCE'));
			return false;
		}
		if($this->isEmpty('target')){
			$this->setError(static::getMessage('ERROR_NO_TARGET'));
			return false;
		}
		$bResult = $this->processSingleElement($intElementId, $this->intIBlockId);
		if($this->get('offers') == 'Y'){
			$arCatalog = Helper::getCatalogArray($this->intIBlockId);
			if($arCatalog['OFFERS_IBLOCK_ID']){
				$arFilter = [
					'IBLOCK_ID' => $arCatalog['OFFERS_IBLOCK_ID'],
					'PROPERTY_'.$arCatalog['OFFERS_PROPERTY_ID'] => $intElementId,
				];
				$resOffers = \CIBlockElement::getList([], $arFilter, false, false, ['ID', 'NAME']);
				while($arOffer = $resOffers->getNext(false, false)){
					$this->processSingleElement($arOffer['ID'], $arCatalog['OFFERS_IBLOCK_ID']);
				}
			}
		}
		return $bResult;
	}
	
	/**
	 *	Process single product | offer
	 */
	protected function processSingleElement($intElementId, $intIBlockId){
		$bResult = false;
		$arValues = $this->getPrices($intElementId, $intIBlockId);
		$this->processPrices($intElementId, $arValues);
		$strField = $this->get('target');
		if($intPropertyId = $this->isProperty($strField)){
			$arProp = IBlock::getPropertyById($intPropertyId, $intIBlockId);
			$strPropType = $arProp['PROPERTY_TYPE'].(strlen($arProp['USER_TYPE']) ? ':'.$arProp['USER_TYPE'] : '');
			$arSaveValue = [];
			foreach($arValues as $obValue){
				switch($strPropType){
					case 'S:Money':
						$arSaveValue[] = sprintf('%s|%s', $obValue->getValue(), $obValue->get('CURRENCY'));
						break;
					default:
						$arSaveValue[] = $obValue->getValue();
						break;
				}
			}
			if($arProp['MULTIPLE'] != 'Y'){
				$arSaveValue = reset($arSaveValue);
			}
			$bResult = $this->setPropertyValue($intElementId, $intPropertyId, $arSaveValue, null, $intIBlockId);
		}
		elseif($intPriceId = $this->isPrice($strField)){
			$obValue = $this->cutMultipleValue($arValues, static::MULTIPLE_MODE_FIRST);
			if($obValue){
				$bResult = Helper::setProductPrice($intElementId, $intPriceId, $obValue->getValue(), $obValue->get('CURRENCY'));
				if(!$bResult){
					$this->setError(sprintf(static::getMessage('ERROR_SET_PRICE', ['#ELEMENT_ID#' => $intElementId])));
				}
			}
		}
		elseif($strCatalogField = $this->isCatalogField($strField)){
			if($strCatalogField == 'PURCHASING_PRICE'){
				$obValue = $this->cutMultipleValue($arValues, static::MULTIPLE_MODE_FIRST);
				if($obValue){
					$arFields = [
						'PURCHASING_PRICE' => $obValue->getValue(),
						'PURCHASING_CURRENCY' => $obValue->get('CURRENCY'),
					];
					$bResult = Helper::setProductField($intElementId, $arFields);
					if(!$bResult){
						$this->setError(sprintf(static::getMessage('ERROR_SET_PRODUCT_FIELD', ['#ELEMENT_ID#' => $intElementId])));
					}
				}
			}
		}
		return $bResult;
	}
	
	/**
	 *	Get prices for element
	 */
	protected function getPrices($intElementId, $intIBlockId){
		$arResult = [];
		$strField = $this->get('source');
		if($intPropertyId = $this->isProperty($strField)){
			$arFeatures = ['PROPERTY_ID' => [$intPropertyId], 'EMPTY_PROPERTIES' => true];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures, true);
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
			foreach($arResult as $key => $arValue){
				if($arProperty['PROPERTY_TYPE'].':'.$arProperty['USER_TYPE'] == 'S:Money'){
					$arPrice = explode('|', $arValue['VALUE']);
					$arValue['VALUE'] = $arPrice[0];
					$arValue['CURRENCY'] = $arPrice[1];
				}
				if(!strlen($arValue['CURRENCY'])){
					if($strCurrency = $this->get('source_currency')){
						$arValue['CURRENCY'] = $strCurrency;
					}
				}
				$arResult[$key] = new ValueItem($arValue);
			}
		}
		elseif($intPriceId = $this->isPrice($strField)){
			$arFeatures = ['PRICES' => [$intPriceId]];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
			$arValue = [
				'FIELD' => $strField,
				'VALUE' => $arElement['PRICES'][$intPriceId]['PRICE'],
				'CURRENCY' => $arElement['PRICES'][$intPriceId]['CURRENCY'],
			];
			$arResult[] = new ValueItem($arValue);
		}
		elseif($strCatalogField = $this->isCatalogField($strField)){
			$arFeatures = ['CATALOG' => true];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures, true);
			$arValue = [
				'FIELD' => $strField,
				'VALUE' => $arElement[$strField],
			];
			if($strCatalogField == 'PURCHASING_PRICE'){
				$arValue['CURRENCY'] = $arElement['CATALOG_PURCHASING_CURRENCY'];
			}
			$arResult[] = new ValueItem($arValue);
		}
		return $arResult;
	}
	
	/**
	 *	
	 */
	protected function processPrices($intElementId, &$arValues){
		// Use discounts
		if($this->get('use_discounts') == 'Y'){
			$strSiteId = $this->get('discounts_site_id');
			foreach($arValues as $obValue){
				if($intPriceId = $this->isPrice($obValue->getField())){
					$fPrice = $obValue->getValue();
					$strCurrency = $obValue->get('CURRENCY');
					$intQuantity = 1;
					$arUserGroups = [2];
					$strRenewal = 'N';
					$arPrices = [
						['PRICE' => $fPrice, 'CURRENCY' => $strCurrency, 'CATALOG_GROUP_ID' => $intPriceId],
					];
					$arOptimalPrice = \CCatalogProduct::getOptimalPrice($intElementId, $intQuantity, $arUserGroups, $strRenewal,
						$arPrices, $strSiteId);
					if(is_array($arOptimalPrice) && is_array($arOptimalPrice['RESULT_PRICE'])){
						$arResultPrice = $arOptimalPrice['RESULT_PRICE'];
						if($arResultPrice['DISCOUNT_PRICE'] != $fPrice){
							$obValue->setValue($arResultPrice['DISCOUNT_PRICE']);
							$obValue->set('CURRENCY', $arResultPrice['CURRENCY']);
						}
					}
				}
			}
		}
		// Change currency
		if($this->get('change_currency') == 'Y'){
			$strCurrencyNew = $this->get('currency_id');
			$arCurrenciesAll = Helper::getCurrencyList();
			if(array_key_exists($strCurrencyNew, $arCurrenciesAll)){
				foreach($arValues as $obValue){
					$strCurrencyOld = $obValue->get('CURRENCY');
					if(!strlen($strCurrencyOld) && !strlen($obValue->getValue())){
						$obValue->setValue(0);
						$obValue->set('CURRENCY', $strCurrencyNew);
					}
					elseif(strlen($strCurrencyOld)){
						$fNewPrice = Helper::convertCurrency($obValue->getValue(), $obValue->get('CURRENCY'), $strCurrencyNew,
							false, false);
						if(ceil($fNewPrice) != $fNewPrice){
							$fNewPrice = number_format($fNewPrice, 2, '.', '');
						}
						$obValue->setValue($fNewPrice);
						$obValue->set('CURRENCY', $strCurrencyNew);
					}
				}
			}
		}
		// Change price
		if($this->get('change_price') == 'Y'){
			$strCurrencyBase = $this->getBaseCurrency(true);
			$arPriceChange = $this->getPriceChange(false);
			foreach($arValues as $obValue){
				$fPrice = floatVal($obValue->getValue());
				$strCurrency = $obValue->get('CURRENCY');
				$strChangeValue = false;
				foreach($arPriceChange as $arItem){
					$fPriceBase = $fPrice;
					if($strCurrency != $strCurrencyBase){
						$fPriceBase = Helper::convertCurrency($fPriceBase, $strCurrency, $strCurrencyBase, false, false);
					}
					$bMin = !strlen($arItem['MIN']) || $fPriceBase >= $arItem['MIN'];
					$bMax = !strlen($arItem['MAX']) || $fPriceBase <= $arItem['MAX'];
					if($bMin && $bMax){
						$strChangeValue = $arItem['VAL'];
					}
				}
				if(strlen($strChangeValue)){
					$fChangedPrice = Helper::interpretPrice($fPrice, $strChangeValue);
					$obValue->setValue($fChangedPrice);
				}
			}
		}
		// Limit
		if($this->get('limit') == 'Y' && $this->get('source') != 'CATALOG_PURCHASING_PRICE'){
			$strLimit = trim($this->get('limit_value'));
			$arFeatures = ['CATALOG' => true];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures, true);
			$fPurchasingPrice = $arElement['CATALOG_PURCHASING_PRICE'];
			$strPurchasingCurrency = $arElement['CATALOG_PURCHASING_CURRENCY'];
			if($fPurchasingPrice) {
				foreach($arValues as $obValue){
					$fPrice = $obValue->getValue();
					$strCurrency = $obValue->get('CURRENCY');
					$fPurchasingPriceConverted = $fPurchasingPrice;
					if($strPurchasingCurrency != $strCurrency){
						$fPurchasingPriceConverted = Helper::convertCurrency($fPurchasingPriceConverted,
							$strPurchasingCurrency, $strCurrency, false, false);
					}
					$fMinPrice = $fPurchasingPriceConverted;
					if(strlen($strLimit)){
						$fMinPrice = Helper::interpretPrice($fPurchasingPriceConverted, $strLimit);
					}
					if($fPrice < $fMinPrice){
						$fPrice = $fMinPrice;
						$obValue->setValue($fPrice);
					}
				}
			}
		}
		// Round
		if($this->get('round') == 'Y'){
			$intRoundPrecision = trim($this->get('round_precision'));
			$strRoundType = $this->get('round_type');
			if(is_numeric($intRoundPrecision)) {
				foreach($arValues as $obValue){
					$fValue = Helper::roundEx($obValue->getValue(), $intRoundPrecision, $strRoundType);
					$obValue->setValue($fValue);
				}
			}
		}
	}
	
	/**
	 *	
	 */
	protected function getPriceChange($bWithEmpty=false){
		$arResult = [];
		$arVal = $this->get('change_price_value');
		$arMin = $this->get('change_price_min');
		$arMax = $this->get('change_price_max');
		if(is_array($arVal) && !empty($arVal)){
			foreach($arVal as $key => $value){
				$arResult[] = [
					'VAL' => $arVal[$key],
					'MIN' => $arMin[$key],
					'MAX' => $arMax[$key],
				];
			}
		}
		elseif($bWithEmpty){
			$arResult[] = [
				'VAL' => '',
				'MIN' => '',
				'MAX' => '',
			];
		}
		return $arResult;
	}
	
	/**
	 *	
	 */
	protected function getBaseCurrency($bJustCode=false){
		$mResult = false;
		foreach(Helper::getCurrencyList() as $arCurrency){
			if($arCurrency['BASE'] == 'Y'){
				$mResult = $bJustCode ? $arCurrency['CURRENCY'] : $arCurrency;
			}
		}
		return $mResult;
	}
	
}

?>