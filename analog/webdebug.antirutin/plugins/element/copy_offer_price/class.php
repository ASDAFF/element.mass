<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class CopyOfferPrice extends PluginElement {
	
	const GROUP = 'CATALOG';
	
	protected $arFieldsFilter = [
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$bResult = true;
		if($this->isEmpty('prices') || !is_array($this->get('prices'))){
			$this->setError(static::getMessage('ERROR_NO_PRICES'));
			return false;
		}
		$arCatalog = Helper::getCatalogArray($this->intIBlockId);
		# This is offers iblock
		if(is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] && $arCatalog['SKU_PROPERTY_ID']){
			$arFeatures = ['PROPERTY_ID' => [$arCatalog['SKU_PROPERTY_ID']]];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
			$intParentElementId = $arElement['PROPERTIES'][$arCatalog['SKU_PROPERTY_ID']]['VALUE'];
			if(is_numeric($intParentElementId) && $intParentElementId){
				$arParentElement = $this->getElementArray($intParentElementId, $arCatalog['PRODUCT_IBLOCK_ID']);
				if($arParentElement['PRICES']){
					foreach($this->get('prices') as $strField){
						if($intPriceId = $this->isPrice($strField)){
							$arPrice = $arParentElement['PRICES'][$intPriceId];
							if(Helper::setProductPrice($intElementId, $intPriceId, $arPrice['PRICE'], $arPrice['CURRENCY'])){
								$bResult = true;
							}
						}
					}
				}
			}
		}
		# This is main iblock
		elseif(is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID'] && $arCatalog['OFFERS_PROPERTY_ID']) {
			$arElement = $this->getElementArray($intElementId);
			$arFilter = [
				'IBLOCK_ID' => $arCatalog['OFFERS_IBLOCK_ID'],
				'PROPERTY_'.$arCatalog['OFFERS_PROPERTY_ID'] => $intElementId,
			];
			$resOffers = \CIBlockElement::getList([], $arFilter, false, false, ['ID']);
			while($arOffer = $resOffers->getNext(false, false)){
				foreach($this->get('prices') as $strField){
					if($intPriceId = $this->isPrice($strField)){
						$arPrice = $arElement['PRICES'][$intPriceId];
						if(Helper::setProductPrice($arOffer['ID'], $intPriceId, floatVal($arPrice['PRICE']), $arPrice['CURRENCY'])){
							$bResult = true;
						}
					}
				}
			}
		}
		return $bResult;
	}
	
	/**
	 *	Get element array with selected prices
	 */
	protected function getElementArray($intElementId, $intIBlockId=null){
		$arPricesId = [];
		$intIBlockId = is_numeric($intIBlockId) ? $intIBlockId : $this->intIBlockId;
		if(is_array($this->get('prices'))){
			foreach($this->get('prices') as $strField){
				if($intPriceId = $this->isPrice($strField)){
					$arPricesId[] = $intPriceId;
				}
			}
			$arFeatures = ['PRICES' => $arPricesId];
			return IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
		}
		return false;
	}
	
}

?>