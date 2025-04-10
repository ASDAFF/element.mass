<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class SetCanBuy extends PluginElement {
	
	const GROUP = 'CATALOG';
	
	const MODE_ALL_PRICES_AND_QUANTITY = 1;
	const MODE_ONE_PRICE_AND_QUANTITY = 2;
	const MODE_ALL_PRICES = 3;
	const MODE_ONE_PRICE = 4;
	const MODE_QUANTITY = 5;
	
	const MODE_VALUE__1_0 = 1;
	const MODE_VALUE__1_EMPTY = 2;
	const MODE_VALUE__QUANTITY = 3;
	const MODE_VALUE__ENUM_Y = 4;
	const MODE_VALUE__ENUM_YES = 5;
	const MODE_VALUE__ENUM_FIRST = 6;
	const MODE_VALUE__ENUM_LAST = 7;
	
	const MODE_OFFERS_N = 1;
	const MODE_OFFERS_Y = 2;
	const MODE_OFFERS_F = 3;
	
	protected $arFieldsFilter = [
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
	];
	
	protected $arFieldsFilter2 = [
		'PROPERTIES' => ['TYPE_FULL' => ['S', 'N', 'L']],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$arModesWithPrices = [
			static::MODE_ALL_PRICES_AND_QUANTITY,
			static::MODE_ONE_PRICE_AND_QUANTITY,
			static::MODE_ALL_PRICES,
			static::MODE_ONE_PRICE,
		];
		if(!count($this->getPricesId()) && in_array($this->getCanBuyMode(), $arModesWithPrices)){
			$this->setError(static::getMessage('ERROR_NO_SOURCE_PRICE'));
			return false;
		}
		if(!$this->get('target')){
			$this->setError(static::getMessage('ERROR_NO_TARGET_FIELD'));
			return false;
		}
		$bResult = $this->processCanBuy($intElementId);
		return $bResult;
	}
	
	/**
	 *	
	 */
	protected function getUseProduct(){
		return in_array($this->get('mode_offers'), [static::MODE_OFFERS_Y, static::MODE_OFFERS_N]);
	}
	
	/**
	 *	
	 */
	protected function getUseOffers(){
		return in_array($this->get('mode_offers'), [static::MODE_OFFERS_Y, static::MODE_OFFERS_F]);
	}
	
	/**
	 *	Get mode
	 */
	protected function getCanBuyMode(){
		return $this->get('mode_source');
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
	 *	Get value
	 */
	protected function processCanBuy($intElementId){
		$bAvailable = false;
		$bUseProduct = $this->getUseProduct();
		$bUseOffers = $this->getUseOffers();
		$arFeatures = $this->getFeatures($intElementId, $this->intIBlockId, true);
		$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
		# Check product
		if($bUseProduct && $this->isProductAvailable($arElement)){
			$bAvailable = true;
		}
		# Check offers
		elseif($bUseOffers && is_array($arElement['OFFERS'])){
			unset($arFeatures['OFFERS']);
			$intOffersIBlockId = $arElement['OFFERS_IBLOCK_ID'];
			foreach($arElement['OFFERS'] as $intOfferId){
				$arOffer = IBlock::getElementArray($intOfferId, $intOffersIBlockId, $arFeatures);
				if($this->isProductAvailable($arOffer)){
					$bAvailable = true;
					break;
				}
			}
		}
		$this->saveCanBuyValue($intElementId, $arElement, $bAvailable);
		return true;
	}
	
	/**
	 *	Is product (offers) available?
	 */
	protected function isProductAvailable($arElement){
		$bResult = false;
		switch($this->getCanBuyMode()){
			case static::MODE_ALL_PRICES_AND_QUANTITY:
				if($arElement['QUANTITY'] > 0){
					$bResult = $this->allPricesAreFilled($arElement['PRICES']);
				}
				break;
			case static::MODE_ONE_PRICE_AND_QUANTITY:
				if($arElement['QUANTITY'] > 0){
					$bResult = $this->onePriceIsFilled($arElement['PRICES']);
				}
				break;
			case static::MODE_ALL_PRICES:
				$bResult = $this->allPricesAreFilled($arElement['PRICES']);
				break;
			case static::MODE_ONE_PRICE:
				$bResult = $this->onePriceIsFilled($arElement['PRICES']);
				break;
			case static::MODE_QUANTITY:
				if($arElement['QUANTITY'] > 0){
					$bResult = true;
				}
				break;
		}
		return $bResult;
	}
	
	/**
	 *	All prices are filled
	 */
	protected function allPricesAreFilled($arPrices){
		$bResult = false;
		if(is_array($arPrices) && !empty($arPrices)){
			$bResult = true;
			foreach($this->getPricesId() as $intPriceId){
				if(!is_array($arPrices[$intPriceId]) || $arPrices[$intPriceId]['PRICE'] <= 0){
					$bResult = false;
					break;
				}
			}
		}
		return $bResult;
	}
	
	/**
	 *	At least one price is filled
	 */
	protected function onePriceIsFilled($arPrices){
		$bResult = false;
		if(is_array($arPrices) && !empty($arPrices)){
			$bResult = false;
			foreach($this->getPricesId() as $intPriceId){
				if(is_array($arPrices[$intPriceId]) && $arPrices[$intPriceId]['PRICE'] > 0){
					$bResult = true;
					break;
				}
			}
		}
		return $bResult;
	}
	
	/**
	 *	Get features for GetElementArray
	 */
	protected function getFeatures($intElementId, $intIBlockId){
		$arFeatures = [];
		$arModesWithQuantity = [
			static::MODE_ALL_PRICES_AND_QUANTITY,
			static::MODE_ONE_PRICE_AND_QUANTITY,
			static::MODE_QUANTITY,
		];
		if(in_array($this->getCanBuyMode(), $arModesWithQuantity)){
			$arFeatures['QUANTITY'] = true;
		}
		$arModesWithPrices = [
			static::MODE_ALL_PRICES_AND_QUANTITY,
			static::MODE_ONE_PRICE_AND_QUANTITY,
			static::MODE_ALL_PRICES,
			static::MODE_ONE_PRICE,
		];
		if(in_array($this->getCanBuyMode(), $arModesWithPrices)){
			$arFeatures['PRICES'] = $this->getPricesId();
		}
		$arFeatures['OFFERS'] = true;
		if(!$arFeatures['QUANTITY'] && $this->get('mode_numeric') == static::MODE_VALUE__QUANTITY){
			$arFeatures['QUANTITY'] = true;
		}
		if(!$arFeatures['QUANTITY'] && $this->get('mode_text') == static::MODE_VALUE__QUANTITY){
			$arFeatures['QUANTITY'] = true;
		}
		return $arFeatures;
	}
	
	/**
	 *	Save calculated value
	 */
	protected function saveCanBuyValue($intElementId, $arElement, $bAvailable){
		$bResult = false;
		$strField = $this->get('target');
		if($intPropertyId = $this->isProperty($strField)){
			$arProperty = IBlock::getPropertyById($intPropertyId, $this->intIBlockId);
			switch($arProperty['PROPERTY_TYPE']){
				case 'S':
					$bResult = $this->saveCanBuyValueS($intElementId, $arElement, $bAvailable, $arProperty);
					break;
				case 'N':
					$bResult = $this->saveCanBuyValueN($intElementId, $arElement, $bAvailable, $arProperty);
					break;
				case 'L':
					$bResult = $this->saveCanBuyValueL($intElementId, $arElement, $bAvailable, $arProperty);
					break;
			}
		}
		return $bResult;
	}
	
	/**
	 *	Save calculated value [S]
	 */	
	protected function saveCanBuyValueS($intElementId, $arElement, $bAvailable, $arProperty){
		$bResult = false;
		$mValue = false;
		switch($this->get('mode_text')){
			case static::MODE_VALUE__1_0:
				$mValue = $bAvailable ? '1' : '0';
				$bResult = true;
				break;
			case static::MODE_VALUE__1_EMPTY:
				$mValue = $bAvailable ? '1' : false;
				$bResult = true;
				break;
			case static::MODE_VALUE__QUANTITY:
				$mValue = $bAvailable ? $arElement['QUANTITY'] : '';
				$bResult = true;
				break;
		}
		$this->setPropertyValue($intElementId, $arProperty['ID'], $mValue);
		return $bResult;
	}
	
	/**
	 *	Save calculated value [N]
	 */	
	protected function saveCanBuyValueN($intElementId, $arElement, $bAvailable, $arProperty){
		$bResult = false;
		$mValue = false;
		switch($this->get('mode_numeric')){
			case static::MODE_VALUE__1_0:
				$mValue = $bAvailable ? 1 : 0;
				$bResult = true;
				break;
			case static::MODE_VALUE__1_EMPTY:
				$mValue = $bAvailable ? 1 : false;
				$bResult = true;
				break;
			case static::MODE_VALUE__QUANTITY:
				$mValue = $bAvailable ? $arElement['QUANTITY'] : 0;
				$bResult = true;
				break;
		}
		$this->setPropertyValue($intElementId, $arProperty['ID'], $mValue);
		return $bResult;
	}
	
	/**
	 *	Save calculated value [L]
	 */	
	protected function saveCanBuyValueL($intElementId, $arElement, $bAvailable, $arProperty){
		$bResult = false;
		$mValue = false;
		$arSort = [];
		$arFilter = [];
		$arSelect = ['ID'];
		$intLimit = false;
		if($bAvailable){
			switch($this->get('mode_list')){
				case static::MODE_VALUE__ENUM_Y:
					$arFilter['XML_ID'] = 'Y';
					$bResult = true;
					break;
				case static::MODE_VALUE__ENUM_YES:
					$arFilter['VALUE'] = Helper::getMessage('WDA_Y');
					$bResult = true;
					break;
				case static::MODE_VALUE__ENUM_FIRST:
					$arSort = ['SORT' => 'ASC', 'ID' => 'ASC'];
					$intLimit = 1;
					$bResult = true;
					break;
				case static::MODE_VALUE__ENUM_LAST:
				$arSort = ['SORT' => 'DESC', 'ID' => 'DESC'];
					$intLimit = 1;
					$bResult = true;
					break;
			}
			$arEnums = IBlock::getPropertyEnums($arProperty['ID'], $arSort, $arFilter, $arSelect, $intLimit);
			if(is_array($arEnums) && !empty($arEnums)){
				foreach($arEnums as $arEnum){
					$mValue = $arEnum['ID'];
					break;
				}
			}
		}
		$this->setPropertyValue($intElementId, $arProperty['ID'], $mValue);
		return $bResult;
	}
	
}

?>