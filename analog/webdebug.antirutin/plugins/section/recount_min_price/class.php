<?php
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginSection;

class RecountMinPrice extends PluginSection {

	protected $arFieldsFilter = [
		'PROPERTIES' => ['TYPE' => ['S', 'N']],
	];

	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************

	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		$arPriceId = $this->get('price_type');
		$strSiteId = $this->get('site_id');
		$strTarget = $this->get('target');
		$bSkipEmptyPrice = $this->get('skip_empty_price') == 'Y';
		#
		if(empty($arPriceId)){
			$this->setError(static::getMessage('ERROR_NO_PRICE_TYPE_FIELD'));
			return false;
		}
		if(!strlen($strSiteId)){
			$this->setError(static::getMessage('ERROR_NO_SITE_FIELD'));
			return false;
		}
		if(!strlen($strTarget)){
			$this->setError(static::getMessage('ERROR_NO_TARGET_FIELD'));
			return false;
		}
		#
		$fMinPrice = null;
		$arFeatures = [
			'OFFERS' => true,
			'PRICES' => $arPriceId,
		];
		if($this->get('use_active') == 'Y'){
			$arFeatures['FIELDS'] = ['ID'];
		}
		$arFilter = $this->buildFilterByParams($intSectionId);
		$resElements = \CIBlockElement::getList([], $arFilter, false, false, ['ID']);
		while($arElement = $resElements->GetNext()){
			$arElementPrices = [];
			$arElement = IBlock::getElementArray($arElement['ID'], $arElement['IBLOCK_ID'], $arFeatures);
			if($this->get('including_main_price') == 'Y'){
				if(is_array($arElement['PRICES']) && !empty($arElement['PRICES'])){
					foreach($arElement['PRICES'] as $arPrice){
						if($bSkipEmptyPrice && floatVal($arPrice['PRICE']) <= 0){
							continue;
						}
						$arElementPrices[] = [
							'ID' => $arPrice['ID'],
							'CATALOG_GROUP_ID' => $arPrice['CATALOG_GROUP_ID'],
							'PRICE' => $arPrice['PRICE'],
							'CURRENCY' => $arPrice['CURRENCY'],
							'PRODUCT_ID' => $arElement['ID'],
						];
					}
				}
			}
			if(is_array($arElement['OFFERS']) && !empty($arElement['OFFERS'])){
				foreach($arElement['OFFERS'] as $intOfferId){
					$arOffer = IBlock::getElementArray($intOfferId, $arElement['OFFERS_IBLOCK_ID'], $arFeatures);
					if($this->get('use_active') == 'Y' && $arOffer['ACTIVE'] != 'Y'){
						continue;
					}
					if(is_array($arOffer['PRICES']) && !empty($arOffer['PRICES'])){
						foreach($arOffer['PRICES'] as $arPrice){
							if($bSkipEmptyPrice && floatVal($arPrice['PRICE']) <= 0){
								continue;
							}
							$arElementPrices[] = [
								'ID' => $arPrice['ID'],
								'CATALOG_GROUP_ID' => $arPrice['CATALOG_GROUP_ID'],
								'PRICE' => $arPrice['PRICE'],
								'CURRENCY' => $arPrice['CURRENCY'],
								'PRODUCT_ID' => $intOfferId,
							];
						}
					}
				}
			}
			if($fProductMinPrice = $this->processProductPrices($arElementPrices)){
				if(is_null($fMinPrice) || $fMinPrice > $fProductMinPrice){
					$fMinPrice = $fProductMinPrice;
				}
			}
		}
		$arUpdateFields = [
			$strTarget => floatVal($fMinPrice),
		];
		if($strPropertyCode = $this->isSectionProperty($strTarget)){
			$arProperty = $this->getProperty($strPropertyCode);
			if($arProperty['USER_TYPE_ID'] == 'money'){
				$arUpdateFields[$strTarget] = sprintf('%s|%s', $arUpdateFields[$strTarget], $this->get('currency_id'));
			}
		}
		$bResult = $this->update($intSectionId, $arUpdateFields);
		return $bResult;
	}

	/**
	 * Process found product (and its offers) prices
	 */
	protected function processProductPrices($arPrices){
		$fMinPrice = null;
		$strCurrency = $this->get('currency_id');
		$bWithDiscounts = $this->get('with_discounts') == 'Y';
		$strSiteId = $this->get('site_id');
		foreach($arPrices as &$arPrice){
			if($arPrice['CURRENCY'] != $strCurrency){
				$arPrice['PRICE'] = Helper::convertCurrency($arPrice['PRICE'], $arPrice['CURRENCY'], $strCurrency);
				$arPrice['CURRENCY'] = $strCurrency;
			}
			if($bWithDiscounts){
				$arOptimalPrice = \CCatalogProduct::GetOptimalPrice($arPrice['PRODUCT_ID'], 1, [], 'N', [$arPrice], $strSiteId, []);
				if($arOptimalPrice['RESULT_PRICE']['DISCOUNT_PRICE'] > 0){
					$arPrice['PRICE'] = $arOptimalPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
					$arPrice['CURRENCY'] = $arOptimalPrice['RESULT_PRICE']['CURRENCY'];
				}
			}
			if(is_null($fMinPrice) || $fMinPrice > $arPrice['PRICE']){
				$fMinPrice = $arPrice['PRICE'];
			}
		}
		unset($arPrice);
		return $fMinPrice;
	}

	/**
	 *	Build filter for elements
	 */
	protected function buildFilterByParams($intSectionId){
		$arResult = [];
		$arResult['IBLOCK_ID'] = $this->intIBlockId;
		$arResult['SECTION_ID'] = $intSectionId;
		if($this->get('include_subsections') == 'Y'){
			$arResult['INCLUDE_SUBSECTIONS'] = 'Y';
		}
		if($this->get('use_active') == 'Y'){
			$arResult['ACTIVE'] = 'Y';
		}
		return $arResult;
	}

	protected function getPriceList() : array {
		$arResult = [
			'PROPERTIES' => [
				'NAME' => Helper::getMessage('WDA_IBLOCK_FIELD_GROUP_PRICES'),
				'ITEMS' => [],
			],
		];
		foreach(Helper::getPriceList() as $arPrice){
			$arResult['PROPERTIES']['ITEMS'][$arPrice['ID']] = [
				'NAME' => $arPrice['NAME_LANG'],
				'CODE' => $arPrice['XML_ID'],
			];
		}
		return $arResult;
	}

	protected function getSiteList() : array {
		$arResult = [];
		$arResult['PROPERTIES']['ITEMS'] = Helper::getSitesList();
		return $arResult;
	}

}

?>
