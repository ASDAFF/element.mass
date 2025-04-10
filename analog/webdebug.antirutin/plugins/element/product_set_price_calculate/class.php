<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class ProductSetPriceCalculate extends PluginElement {

	const GROUP = 'SETS';

	const TYPE_PURCHASING_PRICE = 'PURCHASING_PRICE';

	const ZERO_PRICE = '_ZERO_PRICE_';

	protected $arFieldsFilter = [
		'CATALOG' => ['CODE' => 'PURCHASING_PRICE'],
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
	];
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$bResult = true;
		if(\Bitrix\Main\Loader::IncludeModule('catalog') && class_exists('CCatalogProductSet')) {
			$strCurrency = $this->get('currency_id');
			if($intPriceTypeId = $this->isPrice($this->get('price_type'))){
				if($intSumm = $this->getPrice($intElementId, $intPriceTypeId, $strCurrency)){
					Helper::setProductPrice($intElementId, $intPriceTypeId, $intSumm === static::ZERO_PRICE ? 0 : $intSumm, $strCurrency);
				}
			}
			elseif(($strCatalogField = $this->isCatalogField($this->get('price_type'))) && $strCatalogField == 'PURCHASING_PRICE'){
				if($intSumm = $this->getPrice($intElementId, static::TYPE_PURCHASING_PRICE, $strCurrency)){
					Helper::setProductField($intElementId, [
						'PURCHASING_PRICE' => $intSumm === static::ZERO_PRICE ? 0 : $intSumm,
						'PURCHASING_CURRENCY' => $strCurrency,
					]);
				}
			}
		}
		else{
			$this->setError(static::getMessage('ERROR_NO_CATALOG_SETS'));
			$bResult = false;
		}
		return $bResult;
	}

	protected function getPrice($intElementId, $intPriceTypeId, $strCurrency){
		$intResult = 0;
		switch($this->get('type')){
			case 'group':
				$intResult = $this->getSetPrice($intElementId, $intPriceTypeId, $strCurrency, 1);
				break;
			case 'set':
				$intResult = $this->getSetPrice($intElementId, $intPriceTypeId, $strCurrency, 2);
				break;
			default: # auto
				$intPrice1 = $this->getSetPrice($intElementId, $intPriceTypeId, $strCurrency, 1);
				$intPrice2 = $this->getSetPrice($intElementId, $intPriceTypeId, $strCurrency, 2);
				if($intPrice1 === static::ZERO_PRICE || $intPrice2 === static::ZERO_PRICE){
					$intResult = static::ZERO_PRICE;
				}
				else{
					$intResult = max($intPrice1, $intPrice2);
				}
				break;
		}
		return $intResult;
	}

	protected function getSetPrice($intElementId, $intPriceTypeId, $strCurrency, $intSetType){
		$intResult = 0;
		if($intPriceTypeId === static::TYPE_PURCHASING_PRICE){
			$strPriceType = 'PURCHASING_PRICE';
			$strPriceTypeCurrency = 'PURCHASING_CURRENCY';
		}
		else{
			$strPriceType = sprintf('CATALOG_PRICE_%d', $intPriceTypeId);
			$strPriceTypeCurrency = sprintf('CATALOG_CURRENCY_%d', $intPriceTypeId);
		}
		if($arCurrentProductSets = \CCatalogProductSet::getAllSetsByProduct($intElementId, $intSetType)){
			foreach($arCurrentProductSets as $intSetId => $arSet){
				if(is_array($arSet['ITEMS'])){
					foreach($arSet['ITEMS'] as $arItem){
						if($arItem['ID']){
							if($arElement = \CIBlockElement::getList([], ['ID' => $arItem['ITEM_ID']], false, false, ['ID', $strPriceType, $strPriceTypeCurrency])->fetch()){
								$fPrice = floatVal($arElement[$strPriceType] ?? 0);
								if($fPrice <= 0 && $this->get('zero_price') == 'Y'){
									return static::ZERO_PRICE;
								}
								else{
									if($arElement[$strPriceTypeCurrency] != $strCurrency){
										$fPrice = Helper::convertCurrency($fPrice, $arElement[$strPriceTypeCurrency], $strCurrency);
									}
									$intResult += $fPrice * $arItem['QUANTITY'];
								}
							}
						}
					}
				}
				break;
			}
		}
		return $intResult;
	}

}
