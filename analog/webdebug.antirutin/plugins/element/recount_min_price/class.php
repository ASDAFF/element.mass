<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class RecountMinPrice extends PluginElement {
	
	const GROUP = 'CATALOG';
	
	protected $arFieldsFilter = [
		'PROPERTIES' => ['TYPE_FULL' => ['N']],
		'OFFERS.PROPERTIES' => ['TYPE_FULL' => ['N']],
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
	];
	
	protected $arFieldsFilter2 = [
		'PROPERTIES' => ['TYPE_FULL' => ['S', 'N']],
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if(empty($this->getPricesId()) && empty($this->getPropertyId())){
			$this->setError(static::getMessage('ERROR_NO_SOURCE_PRICE'));
			return false;
		}
		if(!$this->get('target')){
			$this->setError(static::getMessage('ERROR_NO_TARGET_FIELD'));
			return false;
		}
		$bResult = $this->processMinPrice($intElementId);
		return $bResult;
	}
	
	/**
	 *	Get source value for transliteration
	 */
	protected function processMinPrice($intElementId){
		$arMinPrices = [];
		$arFeatures = [
			'FIELDS' => ['ACTIVE', 'AVAILABLE', 'CATALOG_AVAILABLE'],
			'OFFERS' => true,
		];
		# Need for main product price?
		if($this->get('including_main_price') == 'Y'){
			$arPricesId = $this->getPricesId();
			if(!empty($arPricesId)){
				$arFeatures['PRICES'] = $arPricesId;
			}
		}
		# Need for main product props?
		$arPropsId = $this->getPropertyId(false);
		if(!empty($arPropsId)){
			$arFeatures['PROPERTY_ID'] = $arPropsId;
		}
		# Get all data!
		$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
		# Get optimal price for main product
		if($this->isProductPricesConsidering($arElement)){
			if($this->get('including_main_price') == 'Y'){
				# Use prices
				if(!empty($arElement['PRICES'])){
					if($this->get('with_discounts') == 'Y'){
						$arOptimalPrice = Helper::getOptimalPrice($intElementId, $this->get('site_id'), $arElement['PRICES']);
						if(is_array($arOptimalPrice) && $arOptimalPrice['UNROUND_DISCOUNT_PRICE']){
							$arOptimalPrice['PRICE'] = $arOptimalPrice['UNROUND_DISCOUNT_PRICE'];
							$arMinPrices[] = Helper::convertCurrencyArray($arOptimalPrice, $this->get('currency_id'), false);
						}
					}
					else{
						$arItemPrices = [];
						foreach($arElement['PRICES'] as $arPrice){
							$arItemPrices[] = Helper::convertCurrencyArray($arPrice, $this->get('currency_id'), false);
						}
						$arMinPrices[] = min($arItemPrices);
					}
				}
			}
			# Use props
			if(!empty($arPropsId)){
				if(!empty($arMinPropPrices = $this->collectMinPriceForProperties($arElement, $arFeatures['PROPERTY_ID']))){
					$arMinPrices = array_merge($arMinPrices, $arMinPropPrices);
				}
			}
		}
		# Get optimal price for offers
		if(is_array($arElement['OFFERS']) && !empty($arElement['OFFERS'])){
			$arFeatures = [
				'FIELDS' => ['ACTIVE', 'AVAILABLE', 'CATALOG_AVAILABLE'],
			];
			$arPricesId = $this->getPricesId();
			if(!empty($arPricesId)){
				$arFeatures['PRICES'] = $arPricesId;
			}
			if(!empty($arPropsId = $this->getPropertyId(true))){
				$arFeatures['PROPERTY_ID'] = $arPropsId;
			}
			$intOffersIBlockId = $arElement['OFFERS_IBLOCK_ID'];
			foreach($arElement['OFFERS'] as $intOfferId){
				$arOffer = IBlock::getElementArray($intOfferId, $intOffersIBlockId, $arFeatures);
				if($this->isProductPricesConsidering($arOffer)){
					# Use prices
					if($this->get('with_discounts') == 'Y'){
						$arOptimalPrice = Helper::getOptimalPrice($intOfferId, $this->get('site_id'), $arOffer['PRICES']);
						if(is_array($arOptimalPrice) && $arOptimalPrice['UNROUND_DISCOUNT_PRICE']){
							$arOptimalPrice['PRICE'] = $arOptimalPrice['UNROUND_DISCOUNT_PRICE'];
							$arMinPrices[] = Helper::convertCurrencyArray($arOptimalPrice, $this->get('currency_id'), false);
						}
					}
					else{
						$arItemPrices = [];
						foreach($arOffer['PRICES'] as $arPrice){
							$arItemPrices[] = Helper::convertCurrencyArray($arPrice, $this->get('currency_id'), false);
						}
						if(!empty($arItemPrices)){
							$arMinPrices[] = min($arItemPrices);
						}
					}
					# Use props
					if(!empty($arFeatures['PROPERTY_ID'])){
						if(!empty($arMinPropPrices = $this->collectMinPriceForProperties($arOffer, $arFeatures['PROPERTY_ID']))){
							$arMinPrices = array_merge($arMinPrices, $arMinPropPrices);
						}
					}
				}
			}
		}
		$arMinPrices = array_filter($arMinPrices);
		# Calculate min price
		if(!empty($arMinPrices)){
			$fMinPrice = min($arMinPrices);
			$this->saveMinPrice($intElementId, $fMinPrice, $this->get('currency_id'));
		}
		return true;
	}

	/**
	 * Check product prices are considering
	 */
	protected function isProductPricesConsidering($arProduct){
		$bResult = true;
		if($arProduct['ACTIVE'] == 'N'){
			if($this->get('use_active') == 'Y'){
				$bResult = false;
			}
		}
		if($arProduct['AVAILABLE'] == 'N' || $arProduct['CATALOG_AVAILABLE'] == 'N'){
			if($this->get('use_available') == 'Y'){
				$bResult = false;
			}
		}
		return $bResult;
	}
	
	/**
	 *	Get saved prices id
	 */
	protected function getPricesId(){
		$arResult = [];
		if(is_array($this->get('price_type'))){
			foreach($this->get('price_type') as $strField){
				if($intPriceId = $this->isPrice($strField)){
					$arResult[] = $intPriceId;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get saved property id
	 */
	protected function getPropertyId($bOffer=false){
		$arResult = [];
		if(is_array($this->get('price_type'))){
			foreach($this->get('price_type') as $strField){
				if($bOffer){
					if($strOfferField = $this->isOfferField($strField)){
						if($intPropertyId = $this->isProperty($strOfferField)){
							$arResult[] = $intPropertyId;
						}
					}
				}
				else{
					if($intPropertyId = $this->isProperty($strField)){
						$arResult[] = $intPropertyId;
					}
				}
			}
		}
		return $arResult;
	}

	/**
	 * Collect min prices from all selected properties
	 */
	protected function collectMinPriceForProperties($arElement, $arPropertyId){
		$arMinPrices = [];
		foreach($arPropertyId as $intPropId){
			$mValue = $arElement['PROPERTIES'][$intPropId]['VALUE'];
			if(is_numeric($mValue)){
				$arMinPrices[] = $mValue;
			}
			elseif(is_array($mValue)){
				foreach($mValue as $strValue){
					if(is_numeric($strValue)){
						$arMinPrices[] = $strValue;
					}
				}
			}
		}
		return $arMinPrices;
	}
	
	/**
	 *	Save calculated min price
	 */
	protected function saveMinPrice($intElementId, $fMinPrice, $strCurrency){
		$strField = $this->get('target');
		$strCurrency = $this->get('currency_id');
		if($intPropertyId = $this->isProperty($strField)){
			$this->setPropertyValue($intElementId, $intPropertyId, $fMinPrice);
		}
		elseif($intPriceId = $this->isPrice($strField)){
			Helper::setProductPrice($intElementId, $intPriceId, $fMinPrice, $strCurrency);
		}
	}
	
}

?>